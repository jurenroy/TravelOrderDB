<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Employee;

class FormController extends Controller
{
    public function showForm()
    {
        // If you want to display anything initially when the form is loaded
        // For example, fetching divisions
        $divisions = Form::all();
        return view('form', compact('divisions'));
    }
    // Retrieve a specific service
    public function show($id)
    {
        // Fetch employee by name_id
        $employee = Form::where('travel_order_id', $id)->first(); // Using where to match name_id

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404); // Adjusted message
        }

        return response()->json($employee);
    }

    public function getForm($name_id, $status, $limit)
    {
        // Start building the query
        $query = Form::query();

        // Define section chief IDs and members
        $sectionChiefIds = [39, 2, 3, 8, 42, 34, 29, 36, 11, 5, 47];
        $members = [
            [23, 25, 35, 70, 64,78], //Perater 39
            [30, 7, 26, 18, 67, 49, 24], //Alvarez 2 
            [43, 40,71,81], //Asis 3
            [32, 50, 71], //Bondad 8 
            [33, 6], //Serojales 42
            [41, 46], //Orteza 34
            [38, 65, 1, 28], //Ligas 29
            [44, 22, 61, 27], //Paulma 36
            [31], //Cajegas 11
            [16, 63, 19, 9], //Baraacal 5
            [12, 14] //Verdejo 47
        ];
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
    
        // Check if the name_id is in sectionChiefIds
        if (in_array($name_id, $sectionChiefIds)) {
            $index = array_search($name_id, $sectionChiefIds);
            $associatedMembers = $members[$index];
            // Check if the status is "Me"
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            }
            // Check if the status is "Pending"
            else if ($status === 'Pending') {
                // Filter by associated members only (excluding the section chief)
                $query->whereIn('name_id', $associatedMembers) // Only include associated members
                       
                       // Filter by the specified month
                      ->whereNull('initial') // Add condition for "initial" to be null
                      ->where('intervals', 0); // Add condition for "intervals" to be 0 
            } 
            // Check if the status is "Done"
            else if ($status === 'Done') {
                    // Filter by associated members only (excluding the section chief)
                    $query->whereIn('name_id', $associatedMembers) // Only include associated members
                           
                           // Filter by the specified month
                          ->where('initial', 'initialized') // Add condition for "initial" to be "initialized"
                          ->where('intervals', 0); // Add condition for "intervals" to be 0
                }
        }// Check if the  name_id is the noter (37)
        else if ($name_id == 37) {
            // Check if the status is "Me"
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            }
            // Check if the status is "Pending"
            else if ($status === 'Pending') {
            // Filter by associated noter members only (excluding the noter)
            $query 
                   // Filter by the specified month
                  ->whereNull('note') // Add condition for "note" to be null
                  ->where('initial', 'initialized'); // Add condition for "initial" to be "initialized"
            }
            // Check if the status is "Done" and name_id is the noter (37)
            else if ($status === 'Done') {
                // Filter by associated noter members only (excluding the noter)
                $query 
                       // Filter by the specified month
                      ->whereNotNull('note') // Add condition for "note" to be not null
                      ->where('initial', 'initialized'); // Add condition for "initial" to be "initialized"
            }
        }
        else if (in_array($name_id, [23, 64])) {
            // Filter based on selected status
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            }  
            // Filter based on selected status
            else if ($status === 'Pending') {
                // Return forms where name_id matches the user's name_id
                $query->whereNotNull('note') // Ensure note is not null
                      ->where('note', 'like', '%KAYSHE JOY F. PELINGON%') // Check if note contains the specified string
                      ->where('note', 'not like', '%ASHLEY%') // Exclude notes containing this name
                      ->where('note', 'not like', '%DULCE%'); // Exclude notes containing this name
            } 
            // Filter based on selected status
            else if ($status === 'Done') {
                // Return forms where name_id matches the user's name_id
                $query->whereNotNull('note') // Ensure note is not null
                      ->where(function($query) {
                          $query->where('note', 'like', '%ASHLEY%') // Include notes containing this name
                                ->orWhere('note', 'like', '%DULCE%'); // Include notes containing this name
                      });
            }
        }
    // Check if the user is a oic
        else if (in_array($name_id, $chiefNameIds)) {
            $index = array_search($name_id, $chiefNameIds);
            $division = $chiefDivisionIds[$index];
            
            // Filter based on selected status
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            } else if ($status === 'Pending') {

                if ($division == 5) {
                    // If division is 5, check for signature2 being null
                        $query->where('division_id', 5) // Check if division_id is equal to 5
                              ->whereNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                              ->whereNotNull('note') // Check if note is NOT NULL (i.e., there is a note present)
                              ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                               
                               
                              // Start an alternative set of conditions using orWhere
                              ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                                $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                      ->where('name_id', '!=', $name_id)
                                      ->whereNull('signature2') // Check if signature2 is NULL
                                      ->whereNotNull('signature1') // Check if signature1 is NOT NULL
                                       
                                      ; 
                              }) 
                              ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                      ->where('name_id', '!=', $name_id)
                                      ->whereNull('initial') // Check if signature2 is NULL
                                       
                                      ; 
                              })
                              ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                      ->where('name_id', '!=', $name_id)
                                      ->whereNull('signature2') // Check if signature2 is NULL
                                      ->whereNot('intervals',1)
                                       
                                      ; 
                              });

                } else if ($division == 2) {
                    $query->where('division_id', $division)
                          ->whereNotNull('note')
                          ->whereNull('signature1')
                          ->where('intervals', 0)
                          ->where('name_id', '!=', $name_id)
                          ->orWhere(function($query) use ($division, $name_id){
                                $query->where('division_id', $division)
                                  ->where('name_id', '!=', $name_id)
                                  ->whereNull('initial') // Check if signature2 is NULL
                                  ->where('intervals', 1)
                                   
                                  ; 
                          })->orWhere(function($query) use ($division, $name_id, $chiefNameIds){
                            $query->where('name_id', '!=', $name_id)
                              ->whereNotIn('name_id', $chiefNameIds) // Check if name_id is chief
                              ->whereNotNull('note')
                              ->whereNull('signature1') // Check if signature2 is NULL
                              ->where('intervals', 1)
                               
                              ; 
                          })->orWhere(function($query) use ($division, $name_id, $chiefNameIds){
                            $query->where('name_id', '!=', $name_id)
                              ->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                              ->whereNotNull('note')
                              ->whereNull('signature1') // Check if signature2 is NULL
                              ->where('aor', 0)
                              ->where('intervals', 1)
                               
                              ; 
                          });
                          if ($name_id == $OICNameId[0]) {
                            // If division is 5, check for signature2 being null
                                $query->orWhere('division_id', 5) // Check if division_id is equal to 5
                                      ->whereNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                                      ->whereNotNull('note') // Check if note is NOT NULL (i.e., there is a note present)
                                      ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                                       
                                       
                                      // Start an alternative set of conditions using orWhere
                                      ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                                        $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                              ->where('name_id', '!=', $name_id)
                                              ->whereNull('signature2') // Check if signature2 is NULL
                                              ->whereNotNull('signature1') // Check if signature1 is NOT NULL
                                               
                                              ; 
                                      }) 
                                      ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                        $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                              ->where('name_id', '!=', $name_id)
                                              ->whereNull('initial') // Check if signature2 is NULL
                                               
                                              ; 
                                      })
                                      ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                        $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                              ->where('name_id', '!=', $name_id)
                                              ->whereNull('signature2') // Check if signature2 is NULL
                                              ->whereNot('intervals',1)
                                               
                                              ; 
                                      });
      
                          }
                }else {
                    $query->where('division_id', $division)
                          ->whereNotNull('note')
                          ->whereNull('signature1')
                          ->where('intervals', 0)
                          ->where('name_id', '!=', $name_id)
                          ->orWhere(function($query) use ($division, $name_id){
                                $query->where('division_id', $division)
                                  ->where('name_id', '!=', $name_id)
                                  ->whereNull('initial') // Check if signature2 is NULL
                                  ->where('intervals', 1)
                                   
                                  ; 
                          });

                    if ($name_id == $OICNameId[0]) {
                      // If division is 5, check for signature2 being null
                          $query->orWhere('division_id', 5) // Check if division_id is equal to 5
                                ->whereNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                                ->whereNotNull('note') // Check if note is NOT NULL (i.e., there is a note present)
                                ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                                 
                                 
                                // Start an alternative set of conditions using orWhere
                                ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                                  $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                        ->where('name_id', '!=', $name_id)
                                        ->whereNull('signature2') // Check if signature2 is NULL
                                        ->whereNotNull('signature1') // Check if signature1 is NOT NULL
                                         
                                        ; 
                                }) 
                                ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                  $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                        ->where('name_id', '!=', $name_id)
                                        ->whereNull('initial') // Check if signature2 is NULL
                                         
                                        ; 
                                })
                                ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                  $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                        ->where('name_id', '!=', $name_id)
                                        ->whereNull('signature2') // Check if signature2 is NULL
                                        ->whereNot('intervals',1)
                                         
                                        ; 
                                });

                    }
                }

            } 
            else if ($status === 'Done') {
                if ($division == 5) {
                    // If division is 5, check for signature2 being null
                        $query->where('division_id', 5) // Check if division_id is equal to 5
                              ->whereNotNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                              ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                               
                               
                              // Start an alternative set of conditions using orWhere
                              ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                                $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                      ->where('name_id', '!=', $name_id)
                                      ->whereNotNull('signature2') // Check if signature1 is NOT NULL
                                       
                                      ; 
                              }) 
                              ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                                $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                      ->where('name_id', '!=', $name_id)
                                      ->whereNotNull('initial') // Check if signature2 is NULL.
                                      ->where('aor', 1)
                                      ->where('intervals', 1)
                                       
                                      ; 
                              });
                } 
                else if ($division == 2) {
                    $query->where('division_id', $division)
                          ->whereNotNull('signature1')
                          ->where('intervals', 0)
                          ->where('name_id', '!=', $name_id)
                          ->orWhere(function($query) use ($division, $name_id){
                                $query->where('division_id', $division)
                                  ->where('name_id', '!=', $name_id)
                                  ->whereNotNull('initial') // Check if signature2 is NULL
                                  ->where('intervals', 1)
                                   
                                  ; 
                          })->orWhere(function($query) use ($division, $name_id, $chiefNameIds){
                            $query->where('name_id', '!=', $name_id)
                              ->whereNotIn('name_id', $chiefNameIds) // Check if name_id is chief
                              ->whereNotNull('signature1') // Check if signature2 is NULL
                              ->where('intervals', 1)
                               
                              ; 
                          })->orWhere(function($query) use ($division, $name_id, $chiefNameIds){
                            $query->where('name_id', '!=', $name_id)
                              ->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                              ->whereNotNull('signature1') // Check if signature2 is NULL
                              ->where('aor', 0)
                              ->where('intervals', 1)
                               
                              ; 
                          });


                          if ($name_id == $OICNameId[0]) {
                            // If division is 5, check for signature2 being null
                            $query->orWhere('division_id', 5) // Check if division_id is equal to 5
                            ->whereNotNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                            ->where('sname', $name_id)
                            ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                             
                             
                            // Start an alternative set of conditions using orWhere
                            ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                              $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                    ->where('name_id', '!=', $name_id)
                                    ->whereNotNull('signature2') // Check if signature1 is NOT NULL
                                    ->where('sname', $name_id)
                                     
                                    ; 
                            }) 
                            ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                              $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                    ->where('name_id', '!=', $name_id)
                                    ->whereNotNull('initial') // Check if signature2 is NULL.
                                    ->where('aor', 1)
                                    ->where('intervals', 1)
                                     
                                    ; 
                            });
      
                        }
                }
                else { 
                    $query->where('division_id', $division)
                          ->whereNotNull('signature1')
                          ->where('name_id', '!=', $name_id)
                          ->where('intervals', 0)
                          ->orWhere(function($query) use ($division, $name_id){
                                $query->where('division_id', $division)
                                  ->whereNotNull('initial') // Check if signature2 is NULL
                                  ->where('name_id', '!=', $name_id)
                                  ->where('intervals', 1)
                                   
                                  ; 
                          });


                          if ($name_id == $OICNameId[0]) {
                            // If division is 5, check for signature2 being null
                            $query->orWhere('division_id', 5) // Check if division_id is equal to 5
                            ->whereNotNull('signature2') // Check if signature2 is NULL (i.e., no second signature)
                            ->where('sname', $name_id)
                            ->where('name_id', '!=', $name_id) // Ensure name_id is NOT equal to the provided $name_id
                             
                             
                            // Start an alternative set of conditions using orWhere
                            ->orWhere(function($query) use ($name_id){ // Use a closure to group the alternative conditions
                              $query->where('division_id', '!=', 5) // Check if division_id is NOT equal to 5
                                    ->where('name_id', '!=', $name_id)
                                    ->whereNotNull('signature2') // Check if signature1 is NOT NULL
                                    ->where('sname', $name_id)
                                     
                                    ; 
                            }) 
                            ->orWhere(function($query) use ($chiefNameIds, $name_id){ // Use a closure to group the alternative conditions
                              $query->whereIn('name_id', $chiefNameIds) // Check if name_id is chief
                                    ->where('name_id', '!=', $name_id)
                                    ->whereNotNull('initial') // Check if signature2 is NULL.
                                    ->where('aor', 1)
                                    ->where('intervals', 1)
                                     
                                    ; 
                            });
      
                        }
                }

            }
                
            
        } else if ($name_id == 76){
            // Filter based on selected status
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            }  // Filter based on selected status
            else if ($status === 'Pending') {
                // Return forms where name_id matches the user's name_id
                      $query->whereNull('signature2'); // Add condition for "signature2" to be not null
            } // Filter based on selected status
            else  if ($status === 'Done') {
                // Return forms where name_id matches the user's name_id
                $query->whereNotNull('signature2'); // Add condition for "signature2" to be not null
            }
        }
        else{
            // Filter based on selected status
            if ($status === 'Me') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id);
            }  // Filter based on selected status
            else if ($status === 'Pending') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id)
                      ->whereNull('signature2'); // Add condition for "signature2" to be not null

            } // Filter based on selected status
            else  if ($status === 'Done') {
                // Return forms where name_id matches the user's name_id
                $query->where('name_id', $name_id)
                      ->whereNotNull('signature2'); // Add condition for "signature2" to be not null
            }
        }
        
        // Get the filtered results with a limit
    $forms = $query->limit($limit)->get(); // Apply the limit here
    
        // Return the results as JSON
        return response()->json($forms);
    }

    public function submitForm(Request $request)
    {
        // Validate form data
        $validatedData = $request->validate([
            'name_id' => 'required|string',
            'position_id' => 'required|string',
            'division_id' => 'required|string',
            'station' => 'required|string',
            'destination' => 'required|string',
            'purpose' => 'required|string',
            'departure' => 'required|date',
            'arrival' => 'required|date',
            'signature1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Signature 1 is optional
            'signature2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Signature 2 is optional
            'pdea' => 'nullable|string',
            'ala' => 'nullable|string',
            'appropriations' => 'nullable|string',
            'remarks' => 'nullable|string',
            'note' => 'nullable|string',
            'sname' => 'nullable|string',
            'sdiv' => 'nullable|string',
            'to_num' => 'nullable|string',
            'initial' => 'nullable|string',
            'intervals' => 'nullable|string',
            'aor' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('signature1')) {
            $signature1Path = $request->file('signature1')->store('signatures');
            $validatedData['signature1'] = $signature1Path;
        }

        if ($request->hasFile('signature2')) {
            $signature2Path = $request->file('signature2')->store('signatures');
            $validatedData['signature2'] = $signature2Path;
        }

        // Valid name_id values
        $validNameIds = [
            13,10,37,62,53,4,56,58,55,60,59,20,77
        ];
        $validSCNameIds = [
            39,2,3,8,42,34,29,36,11,5,47, 52,51,66,17,72,73,54,80
        ];
        $validDCNameIds = [
            48,15,45,21
        ];
    
        // Set initial to 'initialized' if name_id is in the valid list
        if (in_array((int)$validatedData['name_id'], $validNameIds)) {
            $validatedData['initial'] = 'initialized';
        }else if(in_array((int)$validatedData['name_id'], $validSCNameIds) && $validatedData['intervals'] < 1){
            $validatedData['initial'] = 'initialized';
        } if(in_array((int)$validatedData['name_id'], $validDCNameIds) && ($validatedData['aor'] < 1 || $validatedData['intervals'] < 1)){
            $validatedData['initial'] = 'initialized';
        }

        // Check if the request has signature2 and it's not null, and if name_id is 20
        if ($request->filled('name_id') && $validatedData['name_id'] == 20) {
             // Get the current year
            $currentYear = date('Y');
                
            // Get the maximum value of to_num based on the current year from the database
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');

            // Increment the maximum value by 1 to get the next available number
            $newToNum = $maxToNum + 1;
        
            // Increment to_num by 301 based on the count
            $validatedData['to_num'] = $newToNum;
        }   

        $form = new Form();

        // Fill the form instance with validated data
        $form->fill($validatedData);

        // Save the form to the database
        $form->save();

        // Redirect the user back to the form or any other page
        return redirect('/add_form')->with('success', 'Form submitted successfully!');
    }

    public function update_via_post(Request $request, $id)
    {
        $form = Form::findOrFail($id);
    
        $originalData = $form->getOriginal();
        $updatedData = $request->all();
    
        // Compare original data with updated data and only update the fields that have changed
        foreach ($updatedData as $key => $value) {
            if ($originalData[$key] != $value) {
                $form->$key = $value;
            }
        }

        if ($request->filled('signature2')) {
             // Get the current year
            $currentYear = date('Y');

            // Get the maximum value of to_num based on the current year from the database
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');

            // Increment the maximum value by 1 to get the next available number
            $newToNum = $maxToNum + 1;

            // Increment to_num by 301 based on the count
            $form->to_num = $newToNum;
        
            // Save the form (if necessary)
            $form->save();
        }

        if ($request->filled('initial') && $form->aor == 1 && $form->intervals == 1 && in_array($form->name_id, [15, 21, 45, 48])) {
             // Get the current year
            $currentYear = date('Y');

            // Get the maximum value of to_num based on the current year from the database
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');

            // Increment the maximum value by 1 to get the next available number
            $newToNum = $maxToNum + 1;

            // Increment to_num by 301 based on the count
            $form->to_num = $newToNum;
        }

    
        // Save the updated form
        $form->save();
    
        return response()->json(['message' => 'Resource updated successfully']);
    }

}
