<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
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





}
