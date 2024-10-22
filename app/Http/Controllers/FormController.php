<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;

class FormController extends Controller
{
    public function showForm()
    {
        // If you want to display anything initially when the form is loaded
        // For example, fetching divisions
        $divisions = Form::all();
        return view('form', compact('divisions'));
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
            13,10,37,62,53,75,4,56,58,55,60,59,20,77
        ];
        $validSCNameIds = [
            39,2,3,8,42,34,29,36,5,47,52,51
        ];
        $validDCNameIds = [
            48,15,45,21
        ];
    
        // Set initial to 'initialized' if name_id is in the valid list
        if (in_array((int)$validatedData['name_id'], $validNameIds)) {
            $validatedData['initial'] = 'initialized';
        }else if(in_array((int)$validatedData['name_id'], $validSCNameIds && $validatedData['intervals'] !== 1)){
            $validatedData['initial'] = 'initialized';
        }else if(in_array((int)$validatedData['name_id'], $validDCNameIds && $validatedData['aor'] !== 1 && $validatedData['intervals'] !== 1)){
            $validatedData['initial'] = 'initialized';
        }

        // Check if the request has signature2 and it's not null, and if name_id is 20
        if ($request->filled('name_id') && $validatedData['name_id'] == 20) {
            // Get the current year
            $currentYear = date('Y');
        
            // Get the maximum value of to_num for the current year
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');
        
            // Determine the new to_num
            $newToNum = $maxToNum ? $maxToNum + 1 : 1;

            // Increment the maximum value by 1 to get the next available number
            $newToNum = $maxToNum + 1;
        
            // Increment to_num by 301 based on the count
            $validatedData['to_num'] = $newToNum;
        }   
        // // Check if the request has signature2 and it's not null, and if name_id is 20
        // if ($request->filled('name_id') && $validatedData['name_id'] == 15 && $validatedData['aor'] == 1 && $validatedData['intervals'] == 1) {
        //     // Get the maximum value of to_num from the database
        //     $maxToNum = Form::max('to_num');

        //     // Increment the maximum value by 1 to get the next available number
        //     $newToNum = $maxToNum + 1;
        
        //     // Increment to_num by 301 based on the count
        //     $validatedData['to_num'] = $newToNum;
        // }   

        // Create new form instance
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
        
            // Get the maximum value of to_num for the current year
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');
        
            // Determine the new to_num
            $newToNum = $maxToNum ? $maxToNum + 1 : 1;
        
            // Set the new to_num on the form
            $form->to_num = $newToNum;
        
            // Save the form (if necessary)
            $form->save();
        }

        // Check if the request has signature2 and it's not null
        if ($request->filled('signature1') && $request->filled('name_id') && in_array($request->name_id, [15, 21, 45, 48])) {
            // Get the current year
            $currentYear = date('Y');
        
            // Get the maximum value of to_num for the current year
            $maxToNum = Form::whereYear('date', $currentYear)->max('to_num');
        
            // Determine the new to_num
            $newToNum = $maxToNum ? $maxToNum + 1 : 1;

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
