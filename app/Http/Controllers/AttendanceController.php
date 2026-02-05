<?php

namespace App\Http\Controllers;
use App\Models\Attendance;

use Illuminate\Http\Request;

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
