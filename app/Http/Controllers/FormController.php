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
    
        // Save the updated form
        $form->save();
    
        return response()->json(['message' => 'Resource updated successfully']);
    }

}
