<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Name;
use App\Models\Position;
use App\Models\Division;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string',
            'middle_init' => 'nullable|string',
            'last_name' => 'required|string',
            'position_name' => 'required|string',
            'division_name' => 'required|string',
        ]);

       // Extracting data from the request
        $requestData = $request->all();

        // Check if the name already exists
        $existingName = Name::where('first_name', $requestData['first_name'])
            ->where('middle_init', $requestData['middle_init'] ?? null)
            ->where('last_name', $requestData['last_name'])
            ->first();

        // Create a new name if it doesn't exist
        if (!$existingName) {
            $nameId = DB::table('name')->max('name_id') + 1;
            $name = new Name();
            $name->name_id = $nameId;
            $name->fill($requestData);
            $name->save();
        } else {
            $nameId = $existingName->name_id;
        }

        // Check if the position already exists
        $existingPosition = Position::where('position_name', $requestData['position_name'])->first();

        // Create a new position if it doesn't exist
        if (!$existingPosition) {
            $positionId = DB::table('position')->max('position_id') + 1;
            $position = new Position();
            $position->position_id = $positionId;
            $position->position_name = $requestData['position_name'];
            $position->save();
        } else {
            $positionId = $existingPosition->position_id;
        }

        // Check if the division already exists
        $existingDivision = Division::where('division_name', $requestData['division_name'])->first();

        // Create a new division if it doesn't exist
        if (!$existingDivision) {
            $divisionId = DB::table('division')->max('division_id') + 1;
            $division = new Division();
            $division->division_id = $divisionId;
            $division->division_name = $requestData['division_name'];
            $division->save();
        } else {
            $divisionId = $existingDivision->division_id;
        }

        // Create a new employee
        $employee = new Employee();
        $employee->name_id = $nameId;
        $employee->position_id = $positionId;
        $employee->division_id = $divisionId;
        $employee->save();

        // Return success response
        return response()->json(['message' => 'Employee created successfully']);
    }
    public function update_via_post(Request $request, $name_id)
{   
    Employee::whereNotNull('rd')->update(['rd' => null]);
    try {
        // Find the employee by name_id
        $employee = Employee::where('name_id', $name_id)->firstOrFail();

        // Get the current value of 'rd'
        $current_rd = $employee->rd;

        // Toggle the value of 'rd'
        if ($current_rd === null) {
            $employee->rd = 'in';
        } else {
            $employee->rd = null;
        }

        // Save the updated employee
        $employee->save();

        // Return a success response
        return response()->json(['message' => 'Resource updated successfully', 'name_id' => $name_id]);
    } catch (\Exception $e) {
        // If an exception occurs, return an error response
        return response()->json(['error' => $e->getMessage()], 500);
    }
    
}
public function edit_employee(Request $request)
{
    // Log the request data
    \Log::info('Received data:', $request->all());
    // Validate the incoming request data
    $request->validate([
        'employee_id' => 'string',
        'first_name' => 'string',
        'middle_init' => 'nullable|string',
        'last_name' => 'string',
        'position_name' => 'string',
        'division_name' => 'string',
        'chief' => 'integer',
        'rd' => 'string',
        'isActive' => 'string',
    ]);

    // Find the employee by ID or throw an error if not found
    $employee = Employee::findOrFail($request->input('employee_id'));

    // Update Employee fields
    $employee->update([
        'chief' => $request->input('chief', $employee->chief),
        'rd' => $request->input('rd', $employee->rd),
        'isActive' => $request->input('isActive', $employee->isActive),
    ]);

    // Update or create related models if necessary

    // Update the name if necessary
    if ($request->filled('first_name') || $request->filled('middle_init') || $request->filled('last_name')) {
        $name = Name::find($employee->name_id); // Retrieve the name record based on name_id
        if ($name) {
            // If a name record exists, update it
            $name->update([
                'first_name' => $request->input('first_name', $name->first_name),
                'middle_init' => $request->input('middle_init', $name->middle_init),
                'last_name' => $request->input('last_name', $name->last_name),
            ]);
        }
    }


    // Update or create the position
    if ($request->filled('position_name')) {
        // Check if the position already exists
        $existingPosition = Position::where('position_name', $request->input('position_name'))->first();
        
        // If the position exists, update the employee's position_id with the existing position's ID
        if ($existingPosition) {
            $employee->position_id = $existingPosition->position_id;
        } else {
            // If the position does not exist, create a new position and update the employee's position_id
            $position = new Position();
            $position->position_name = $request->input('position_name');
            // Calculate the next position_id based on the maximum position_id in the table
            $position->position_id = Position::max('position_id') + 1;
            $position->save();
            $employee->position_id = $position->position_id;
        }
    }

    // Update or create the division
    if ($request->filled('division_name')) {
        // Check if the division already exists
        $existingDivision = Division::where('division_name', $request->input('division_name'))->first();
        
        // If the division exists, update the employee's division_id with the existing division's ID
        if ($existingDivision) {
            $employee->division_id = $existingDivision->division_id;
        } else {
            // If the division does not exist, create a new division and update the employee's division_id
            $division = new Division();
            $division->division_name = $request->input('division_name');
            // Calculate the next division_id based on the maximum division_id in the table
            $division->division_id = Division::max('division_id') + 1;
            $division->save();
            $employee->division_id = $division->division_id;
        }
    }

    // Save the changes to the employee
    $employee->save();

    // Return a success message
    return response()->json(['message' => 'Employee updated successfully']);

    
}


}
