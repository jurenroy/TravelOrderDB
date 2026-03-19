<?php

namespace App\Http\Controllers;

use App\Models\Dtr;
use Illuminate\Http\Request;

class DtrController extends Controller
{
    public function index()
    {
        return response()->json(Dtr::all());
    }

    public function store(Request $request)
    {
        // Validate that all fields are required
        $validated = $request->validate([
            'name_id'   => 'required|integer',   // must be provided and be an integer
            'start_date'=> 'required|date',      // must be provided and be a valid date
            'end_date'  => 'required|date',      // must be provided and be a valid date
        ]);
    
        // Create the Dtr record with validated data
        $dtr = Dtr::create($validated);

        // Dispatch a job to process merged attendance for this Dtr
        \App\Jobs\ProcessMergedAttendance::dispatch($dtr->id);
    
        return response()->json($dtr);
    }

    public function show($id)
    {
        $dtr = Dtr::find($id);

        if (!$dtr) {
            return response()->json([
                'message' => 'DTR not found.'
            ]);
        }

        return response()->json($dtr);
    }

    public function showDtrByDate(Request $request)
    {
        $nameId    = $request->name_id;
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $query = Dtr::query();

        if ($nameId) {
            $query->where('name_id', $nameId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate]);
        }

        $dtrs = $query->get();

        return response()->json($dtrs);
    }

    public function update(Request $request, $id)
    {
        $dtr = Dtr::find($id);

        if (!$dtr) {
            return response()->json([
                'message' => 'DTR not found.'
            ]);
        }

        $validated = $request->validate([
            'name_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $dtr->update($validated);

        return response()->json($dtr);
    }

    public function destroy($id)
    {
        $dtr = Dtr::find($id);

        if (!$dtr) {
            return response()->json([
                'message' => 'DTR not found.'
            ]);
        }

        $dtr->delete();

        return response()->json([
            'message' => 'DTR deleted successfully.'
        ]);
    }


    public function generateDTRCOS(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ]);

    $employees = \App\Models\Employee::where('status', 0)
    ->where('isActive', '!=', 'out')
    ->get();


    $created = [];

    foreach ($employees as $employee) {

        $dtr = Dtr::create([
            'name_id' => $employee->name_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        // run attendance job
        \App\Jobs\ProcessMergedAttendance::dispatch($dtr->id);

        $created[] = $dtr;
    }

    return response()->json([
        'message' => 'Bulk DTR created for status 0 employees',
        'count' => count($created),
        'data' => $created
    ]);
}

public function generateDTRREG(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ]);

    $employees = \App\Models\Employee::where('status', 1)
    ->where('isActive', '!=', 'out')
    ->get();


    $created = [];

    foreach ($employees as $employee) {

        $dtr = Dtr::create([
            'name_id' => $employee->name_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        \App\Jobs\ProcessMergedAttendance::dispatch($dtr->id);

        $created[] = $dtr;
    }

    return response()->json([
        'message' => 'Bulk DTR created for status 1 employees',
        'count' => count($created),
        'data' => $created
    ]);
}

public function generateDTROTH(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ]);

    $employees = \App\Models\Employee::where('status', 2)
    ->where('isActive', '!=', 'out')
    ->get();

    $created = [];

    foreach ($employees as $employee) {

        $dtr = Dtr::create([
            'name_id' => $employee->name_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        \App\Jobs\ProcessMergedAttendance::dispatch($dtr->id);

        $created[] = $dtr;
    }

    return response()->json([
        'message' => 'Bulk DTR created for status 2 employees',
        'count' => count($created),
        'data' => $created
    ]);
}
}
