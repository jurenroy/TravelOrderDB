<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    // Store a new attendance record
public function store(Request $request)
    {
        // Validate incoming request (we only validate 'name_id' and 'status')
        $request->validate([
            'name_id' => 'required', // Assuming 'name_id' is the user ID
            'status' => 'required', // Valid statuses
        ]);

        // Create and store the attendance record with automatic date and timestamp
        $attendance = Attendance::create([
            'name_id' => $request->name_id,
            'status' => $request->status,
            'date' => now()->toDateString(), // Automatically set the current date
        ]);

        // Return a response
        return response()->json([
            'message' => 'Attendance recorded successfully.',
            'data' => $attendance,
        ], 201);
    }

    // Retrieve all attendance records
    public function index(Request $request)
    {
        // Fetch all attendance records
        $attendances = Attendance::all();

        // Return a response with the data
        return response()->json($attendances);
    }

    // Retrieve a specific attendance record by ID
    public function show(Request $request, $id)
    {
        // Find the attendance by ID
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found.'], 404);
        }

        // Return the attendance record
        return response()->json($attendance);
    }

    public function showAttendanceByDate($name_id, $date)
    {
        // Validate the `date` format
        if (!\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json([
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }
    
        // Fetch all attendance records for the specified user and date
        $attendanceRecords = Attendance::where('name_id', $name_id)
                                        ->where('date', $date)
                                        ->get(); // Get all records for that date
    
        // Check if there are any records
        if ($attendanceRecords->isEmpty()) {
            return response()->json([
                'message' => 'No attendance records found for the selected date.',
            ], 404);
        }
    
        return response()->json($attendanceRecords); // Return the attendance records for that date
    }

    public function filterAttendance(Request $request)
{
    // Validate request
    $request->validate([
        'name_id'    => 'required|string',   // "50" or "50,47,94"
        'start_date' => 'required|date',
        'end_date'   => 'required|date',
    ]);

    // Call the external API
    $response = Http::get('http://172.31.10.43:5000/attendance/filter', [
        'name_id'    => $request->name_id,
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
    ]);

    // Handle failed response
    if ($response->failed()) {
        return response()->json([
            'message' => 'Failed to fetch attendance data',
            'error'   => $response->body(),
        ], $response->status());
    }

    // Return API response
    return response()->json($response->json());
}


public function mergedAttendance(Request $request)
{
    // 1️⃣ Validate request params
    $request->validate([
        'name_id'    => 'required|integer', // this is employee.name_id
        'start_date' => 'required|date',
        'end_date'   => 'required|date',
    ]);

    $nameId     = $request->name_id; // internal employee.name_id
    $startDate  = $request->start_date;
    $endDate    = $request->end_date;

    // 2️⃣ Get employee by employee.name_id
    // Get the bio_id for the given employee.name_id
    $bioId = Employee::where('name_id', $nameId)->value('bio_id');

    if (!$bioId) {
        return response()->json(['message' => 'Employee not found or bio_id missing'], 404);
    }

    // 3️⃣ Fetch LOCAL attendance for the employee.name_id
    $localAttendance = Attendance::where('name_id', $nameId)
        ->whereBetween('date', [$startDate, $endDate])
        ->select('name_id', 'date', 'timestamp')
        ->get()
        ->map(fn($row) => [
            'name_id'   => $row->name_id, // keep as internal name_id
            'date'      => $row->date,
            'timestamp' => $row->timestamp,
            'source'    => 'local',
        ]);


    // 4️⃣ Fetch REMOTE attendance from API using employee.bio_id
    $response = Http::timeout(10)->get('http://172.31.10.43:5000/attendance/filter', [
        'name_id'    => $bioId,
        'start_date' => $startDate,
        'end_date'   => $endDate,
    ]);


    if ($response->failed()) {
        return response()->json([
            'message' => 'Failed to fetch attendance from API',
            'error'   => $response->body(),
        ], $response->status());
    }

    // 5️⃣ Map API data to internal name_id
    $apiAttendance = collect($response->json('data'))
        ->map(fn($row) => [
            'name_id'   => $nameId, // map bio_id → internal name_id
            'date'      => $row['date'],
            'timestamp' => $row['timestamp'],
            'source'    => 'api',
        ]);

    $localAttendance = collect($localAttendance);
    $apiAttendance   = collect($apiAttendance);

    // 6️⃣ Merge + deduplicate
    $merged = 
        $localAttendance
        ->merge($apiAttendance)
        ->unique(fn($r) => $r['name_id'].'|'.$r['date'].'|'.$r['timestamp'])
        ->sortBy('timestamp')
        ->values();

    return $merged;

    // 7️⃣ Remove junk dates like 2000-01-01
    $merged = $merged->reject(fn($r) => $r['date'] === '2000-01-01')->values();

    // 8️⃣ Return clean data
    return response()->json([
        'count' => $merged->count(),
        'data'  => $merged->map(fn($r) => [
            'name_id'   => $r['name_id'], // always internal employee.name_id
            'date'      => $r['date'],
            'timestamp' => $r['timestamp'],
        ]),
    ]);
}
    
// Add this method to AttendanceController
public function mergedAttendanceHelper($nameId, $startDate, $endDate)
{
    $bioId = Employee::where('name_id', $nameId)->value('bio_id');
    if (!$bioId) return [];

    // Local attendance
    $local = Attendance::where('name_id', $nameId)
        ->whereBetween('date', [$startDate, $endDate])
        ->get(['name_id','date','timestamp'])
        ->map(fn($r) => [
            'name_id'   => $r->name_id,
            'date'      => $r->date,
            'timestamp' => $r->timestamp,
            'status'    => '', 
        ]);

    // Remote API
    $response = \Illuminate\Support\Facades\Http::timeout(10)
        ->get('http://172.31.10.43:5000/attendance/filter', [
            'name_id'    => $bioId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

    $api = collect($response->json('data', []))
        ->map(fn($r) => [
            'name_id'   => $nameId,
            'date'      => $r['date'],
            'timestamp' => $r['timestamp'],
            'status'    => '',
        ]);

    // Merge + deduplicate + remove junk
    $merged = collect($local)
        ->merge($api)
        ->unique(fn($r) => $r['name_id'].'|'.$r['date'].'|'.$r['timestamp'])
        ->reject(fn($r) => $r['date'] === '2000-01-01')
        ->sortBy('timestamp')
        ->values();

    return $merged->toArray();
}

    // Update an existing attendance record
    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'status' => 'required', // Valid statuses
        ]);

        // Find the attendance record by ID
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found.'], 404);
        }

        // Update the attendance status
        $attendance->update([
            'status' => $request->status,
        ]);

        // Return the updated attendance record
        return response()->json([
            'message' => 'Attendance updated successfully.',
            'data' => $attendance,
        ]);
    }

    // Delete an attendance record
    public function destroy(Request $request, $id)
    {
        // Find the attendance record by ID
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found.'], 404);
        }

        // Delete the attendance record
        $attendance->delete();

        // Return a success message
        return response()->json(['message' => 'Attendance deleted successfully.']);
    }
}
