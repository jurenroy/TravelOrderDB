<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;
use App\Models\Employee;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;


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
    public function show(Request $request, $name_id, $status, $limit, $offset, $countOnly = false)
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
        $division = $index !== false ? $chiefDivisionIds[$index] : null;

        $divisionMembers = $division ? Employee::where('division_id', $division)->get() : collect();
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

        // If we only need the count, return it directly
    if ($countOnly) {
        return $query->count();
    }
    // Now, apply search if provided
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        // If it's a string, search by the Names model
        $names = \App\Models\Name::where(function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(middle_init) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        })->get();

        // Get all matching name_ids from the Names model
        $nameIds = $names->pluck('name_id')->toArray();

        // If we found matching name_ids, filter RSOs accordingly
        if (count($nameIds) > 0) {
            // Apply the filter to the query using whereIn
            $forms = $query->whereIn('name_id', $nameIds)  // Use whereIn for the filtering
                           ->orderBy('leaveform_id', 'desc')
                           ->get();
        
            // Apply offset and limit to the result collection (in-memory pagination)
            $paginatedForms = $forms->slice($offset, $limit)->values();
                        
            // Return the paginated results as JSON
            return response()->json($paginatedForms);
        }
         else {
            // If no names match, return empty or handle as needed
            return response()->json([]);
        }
    }
        return $query->orderBy('leaveform_id', 'desc')->offset($offset)->limit($limit)->get(); // Apply the limit here
    }

    public function getCount($name_id)
    {
        // Call the getForm method with 'Pending' status and only ask for the count
        return $this->show($name_id, 'Pending', 0, 0, true);
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

        // Audit log
        AuditLog::create([
            'model' => 'leaveform',
            'model_id' => $leaveForm->leaveform_id,
            'action' => 'created',
            'new_values' => $validatedData,
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Leave Form Created', 'A new leave form has been created for ' . $leaveForm->type);

        // Send admin notification to name_id 76
        $this->sendNotification('Leave Form Created', 'A new leave form has been created for ' . $leaveForm->type, true);

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

    $oldValues = $leaveForm->toArray();

    // Update the fields
    $leaveForm->update($validatedData);

    // Audit log
    AuditLog::create([
        'model' => 'leaveform',
        'model_id' => $leaveForm->leaveform_id,
        'action' => 'updated',
        'old_values' => $oldValues,
        'new_values' => $validatedData,
        'user_id' => auth()->id(),
    ]);

    // Check for status changes and send targeted notifications
    $this->sendStatusNotifications($leaveForm, $oldValues, $validatedData);

    // Send notification via websocket
    $this->sendNotification('Leave Form Updated', 'Leave form for ' . $leaveForm->type . ' has been updated.');

    // Send admin notification to name_id 76
    $this->sendNotification('Leave Form Updated', 'Leave form for ' . $leaveForm->type . ' has been updated.', true);

    // Return the updated leave form
    return response()->json($leaveForm, 200);
    }

    public function sendNotification($title, $message, $isAdmin = false, $target_name_id = null)
    {
        // Send HTTP request to Django websocket server to broadcast notification
        // Assuming Django is running on 172.31.10.34:8012
        $url = $isAdmin ? 'http://172.31.10.34:8012/api/send-notification-admin/' : 'http://172.31.10.34:8012/api/send-notification/';
        $data = [
            'title' => $title,
            'message' => $message,
        ];
        if ($target_name_id) {
            $data['target_name_id'] = $target_name_id;
        }
        try {
            Http::post($url, $data);
            return true; // Return true on success
        } catch (\Exception $e) {
            // Log error if notification fails
            \Log::error('Failed to send notification: ' . $e->getMessage());
            return false; // Return false on failure
        }
    }

    private function sendStatusNotifications($leaveForm, $oldValues, $newValues)
    {
        $creatorNameId = $leaveForm->name_id;

        // Check if leave credits were filled (any of the credit fields changed from null to not null)
        $creditFields = ['asof', 'tevl', 'tesl', 'ltavl', 'ltasl', 'bvl', 'vsl', 'dayswpay', 'dayswopay', 'others'];
        $creditsFilled = false;
        foreach ($creditFields as $field) {
            if (is_null($oldValues[$field]) && !is_null($newValues[$field] ?? null)) {
                $creditsFilled = true;
                break;
            }
        }
        if ($creditsFilled) {
            // Notify creator that leave credits have been filled
            $this->sendNotification('Leave Credits Filled', 'Your leave form for ' . $leaveForm->type . ' has been filled with leave credits.', false, $creatorNameId);
            // Notify name_id 2 for certification
            $this->sendNotification('Leave Form Ready for Certification', 'A leave form for ' . $leaveForm->type . ' is ready for certification.', false, 2);
        }

        // Check if certification was added
        if (is_null($oldValues['certification']) && !is_null($newValues['certification'] ?? null)) {
            // Notify creator that certification has been done
            $this->sendNotification('Leave Form Certified', 'Your leave form for ' . $leaveForm->type . ' has been certified.', false, $creatorNameId);
            // Determine next approvers
            $employee = Employee::where('name_id', $creatorNameId)->first();
            if ($employee && $employee->division_id == 5) {
                // Notify employees with rd
                $rds = Employee::whereNotNull('rd')->get();
                foreach ($rds as $rd) {
                    $this->sendNotification('Leave Form Ready for Recommendation', 'A leave form for ' . $leaveForm->type . ' is ready for recommendation.', false, $rd->name_id);
                }
            } else {
                // Notify chiefs
                $chiefs = Employee::where('chief', '1')->get();
                foreach ($chiefs as $chief) {
                    $this->sendNotification('Leave Form Ready for Recommendation', 'A leave form for ' . $leaveForm->type . ' is ready for recommendation.', false, $chief->name_id);
                }
            }
        }

        // Check if recommendation was added
        if (is_null($oldValues['recommendation']) && !is_null($newValues['recommendation'] ?? null)) {
            // Notify creator that recommendation has been done
            $this->sendNotification('Leave Form Recommended', 'Your leave form for ' . $leaveForm->type . ' has been recommended.', false, $creatorNameId);
            // Notify approvers (same logic as above)
            $employee = Employee::where('name_id', $creatorNameId)->first();
            if ($employee && $employee->division_id == 5) {
                $rds = Employee::whereNotNull('rd')->get();
                foreach ($rds as $rd) {
                    $this->sendNotification('Leave Form Ready for Approval', 'A leave form for ' . $leaveForm->type . ' is ready for approval.', false, $rd->name_id);
                }
            } else {
                $chiefs = Employee::where('chief', '1')->get();
                foreach ($chiefs as $chief) {
                    $this->sendNotification('Leave Form Ready for Approval', 'A leave form for ' . $leaveForm->type . ' is ready for approval.', false, $chief->name_id);
                }
            }
        }

        // Check if approval was added
        if (is_null($oldValues['appsig']) && !is_null($newValues['appsig'] ?? null)) {
            if (!is_null($newValues['disapproved'] ?? null)) {
                // Notify creator of disapproval
                $this->sendNotification('Leave Form Disapproved', 'Your leave form for ' . $leaveForm->type . ' has been disapproved.', false, $creatorNameId);
            } else {
                // Notify creator of approval
                $this->sendNotification('Leave Form Approved', 'Your leave form for ' . $leaveForm->type . ' has been approved.', false, $creatorNameId);
            }
        }
    }
}
