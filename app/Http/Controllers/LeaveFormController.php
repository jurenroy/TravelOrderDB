<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;
use App\Models\Employee;


class LeaveFormController extends Controller
{
    // Retrieve a specific service
    public function shows($id)
    {
        // Fetch employee by name_id
        $employee = LeaveForm::where('leaveform_id', $id)->first(); // Using where to match name_id

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404); // Adjusted message
        }

        return response()->json($employee);
    }


    // Retrieve a specific service
    public function show($name_id, $status, $limit)
    {
        $query = LeaveForm::query();
        // Retrieve employees where rd is not null
        $employeesWithRd = Employee::whereNotNull('rd')->get(); // Get all columns for employees with non-null rd

        // Retrieve employees where chief is '1'
        $employeesWithChief = Employee::where('chief', '1')->get(); // Get all columns for employees who are chiefs

        // Convert to arrays if needed
        $employeesWithRdArray = $employeesWithRd->toArray();
        $employeesWithChiefArray = $employeesWithChief->toArray();

        // Extract name_ids from the employeesWithRdArray for easy access
        $OICNameId = array_column($employeesWithRdArray, 'name_id'); // Get an array of name_id

        // Extract name_ids from the employeesWithChiefArray for easy access
        $chiefNameIds = array_column($employeesWithChiefArray, 'name_id'); // Get an array of name_id
        $chiefDivisionIds = array_column($employeesWithChiefArray, 'division_id'); // Get an array of division_id

        $index = array_search($name_id, $chiefNameIds);
        $division = $chiefDivisionIds[$index];

        $divisionMembers = Employee::where('division_id', $division)->get();
        $divisionMembersArray = $divisionMembers->toArray();
        $divisionMembersIds = array_column($divisionMembersArray, 'name_id'); // Get an array of name_id

        $ORDMembers = Employee::where('division_id', 5)->get();
        $ORDMembersArray = $ORDMembers->toArray();
        $ORDMembersIds = array_column($ORDMembersArray, 'name_id'); // Get an array of name_id

    
        if (in_array($name_id, [24])) {
            if ($status === 'Me') {
                $query->where('name_id', $name_id);
            } elseif ($status === 'Pending') {
                $query->whereNull('asof')
                      ->whereNull('tevl')
                      ->whereNull('tesl')
                      ->whereNull('ltavl')
                      ->whereNull('ltasl')
                      ->whereNull('bvl')
                      ->whereNull('vsl')
                      ->whereNull('dayswpay')
                      ->whereNull('dayswopay')
                      ->whereNull('others');
            } elseif ($status === 'Done') {
                $query->where(function ($q) {
                    $q->whereNotNull('asof')
                      ->orWhereNotNull('tevl')
                      ->orWhereNotNull('tesl')
                      ->orWhereNotNull('ltavl')
                      ->orWhereNotNull('ltasl')
                      ->orWhereNotNull('bvl')
                      ->orWhereNotNull('vsl')
                      ->orWhereNotNull('dayswpay')
                      ->orWhereNotNull('dayswopay')
                      ->orWhereNotNull('others');
                });
            }
        } elseif (in_array($name_id, [2])) {
            if ($status === 'Me') {
                $query->where('name_id', $name_id);
            } elseif ($status === 'Pending') {
                $query->whereNotNull('asof')
                      ->whereNull('certification');
            } elseif ($status === 'Done') {
                $query->whereNotNull('certification');
            }
        } else if (in_array($name_id, $chiefNameIds)) {
            if ($status === 'Me') {
                $query->where('name_id', $name_id);
            } else if ($status === 'Pending') {
                if ($division == 5) {
                    $query->where(function ($q) use ($ORDMembersIds) {
                        $q->whereNotNull('recommendation')
                          ->whereNull('appsig')
                          ->orWhere(function($q) use ($ORDMembersIds) {
                              $q->whereIn('name_id', $ORDMembersIds)
                                ->whereNotNull('certification')
                                ->whereNull('appsig');
                          })
                          ->orWhere(function ($q) {
                            $q->whereIn('name_id', [15, 21, 45, 48])
                              ->whereNotNull('certification')
                              ->whereNull('appsig');
                        });
                    });
                } else{
                    $query->whereIn('name_id', $divisionMembersIds)
                    ->whereNull('recommendation')
                    ->whereNotNull('certification')
                    ->where('name_id', '!=', $name_id);

                    if ($name_id == $OICNameId[0]) {
                        $query->orWhere(function($q) use ($ORDMembersIds) {
                            $q->whereNotNull('recommendation')
                              ->whereNull('appsig')
                              ->orWhere(function ($q) use ($ORDMembersIds) {
                                  $q->whereIn('name_id', $ORDMembersIds)
                                    ->whereNotNull('certification')
                                    ->whereNull('appsig');
                              })
                              ->orWhere(function ($q) {
                                $q->whereIn('name_id', [15, 21, 45, 48])
                                  ->whereNotNull('certification')
                                  ->whereNull('appsig');
                            });
                        });
                    }
                }
            } elseif ($status === 'Done') {
                if ($division == 5) {
                    $query->whereNotNull('appsig');
                } else {
                    $query->whereIn('name_id', $divisionMembersIds)
                    ->whereNotNull('recommendation')
                    ->where('name_id', '!=', $name_id);
                }
            }
        }  else if ($name_id == 76){
            if ($status === 'Me') {
                $query->where('name_id', $name_id);
            } elseif ($status === 'Pending') {
                $query->where(function ($q) {
                          $q->whereNull('appsig');
                      });
            } elseif ($status === 'Done') {
                $query->where(function ($q) {
                          $q->whereNotNull('appsig');
                      });
            }
        }
        else {
            if ($status === 'Me') {
                $query->where('name_id', $name_id);
            } elseif ($status === 'Pending') {
                $query->where('name_id', $name_id)
                      ->where(function ($q) {
                          $q->whereNull('appsig');
                      });
            } elseif ($status === 'Done') {
                $query->where('name_id', $name_id)
                      ->where(function ($q) {
                          $q->whereNotNull('appsig');
                      });
            }
        }
    
        return $query->orderBy('leaveform_id', 'desc')->limit($limit)->get(); // Apply the limit here
    }

    public function store(Request $request)
    {
        // Validate the incoming request, allowing for nullable fields
        $validatedData = $request->validate([
            'name_id' => 'nullable|integer',
            'position_id' => 'nullable|integer',
            'type' => 'nullable|string',
            'detail' => 'nullable|string',
            'description' => 'nullable|string',
            'days' => 'nullable|string',
            'dates' => 'nullable|string',
            'commutation' => 'nullable|string',
            'applicant' => 'nullable|string',
            'asof' => 'nullable|string',
            'tevl' => 'nullable|string',
            'tesl' => 'nullable|string',
            'ltavl' => 'nullable|string',
            'ltasl' => 'nullable|string',
            'bvl' => 'nullable|string',
            'vsl' => 'nullable|string',
            'certification' => 'nullable|string',
            'reco' => 'nullable|string',
            'recodesc' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'dayswpay' => 'nullable|integer',
            'dayswopay' => 'nullable|integer',
            'others' => 'nullable|string',
            'disapproved' => 'nullable|string',
            'approval' => 'nullable|string',
            'appsig' => 'nullable|string',
            'appby' => 'nullable|string',
        ]);

        // Create a new leave form record with the validated data
        $leaveForm = LeaveForm::create($validatedData);

        return response()->json($leaveForm, 201);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request, allowing for nullable fields
        $validatedData = $request->validate([
            'name_id' => 'nullable|integer',
            'position_id' => 'nullable|integer',
            'type' => 'nullable|string',
            'detail' => 'nullable|string',
            'description' => 'nullable|string',
            'days' => 'nullable|string',
            'dates' => 'nullable|string',
            'commutation' => 'nullable|string',
            'applicant' => 'nullable|string',
            'asof' => 'nullable|string',
            'tevl' => 'nullable|string',
            'tesl' => 'nullable|string',
            'ltavl' => 'nullable|string',
            'ltasl' => 'nullable|string',
            'bvl' => 'nullable|string',    
            'vsl' => 'nullable|string',
            'certification' => 'nullable|string',
            'reco' => 'nullable|string',
            'recodesc' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'dayswpay' => 'nullable|integer',
            'dayswopay' => 'nullable|integer',
            'others' => 'nullable|string',
            'disapproved' => 'nullable|string',
            'approval' => 'nullable|string',
            'appsig' => 'nullable|string',
            'appby' => 'nullable|string',
        ]);

        // Find the leave form record by ID
    $leaveForm = LeaveForm::findOrFail($id);
 
    // Manually remove 'days' and 'dates' from the data if they are empty or null
    if (empty($validatedData['days'])) {
        unset($validatedData['days']);
    }

    if (empty($validatedData['dates'])) {
        unset($validatedData['dates']);
    }

    // Update the fields
    $leaveForm->update($validatedData);

    // Return the updated leave form
    return response()->json($leaveForm, 200);
    }
}
