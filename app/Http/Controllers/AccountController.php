<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    public function showForm()
    {
        // If you want to display anything initially when the form is loaded
        // For example, fetching divisions
        $divisions = Account::all();
        return view('account', compact('divisions'));
    }

    public function submitForm(Request $request)
    {
        // Validate form data
        $validatedData = $request->validate([
            'type_id' => 'required|string',
            'name_id' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            
        ]);

  
        // Create new form instance
        $accounts = new Account();

        // Fill the form instance with validated data
        $accounts->fill($validatedData);

        // Save the form to the database
        $accounts->save();

        // Redirect the user back to the form or any other page
        return redirect('/add_account')->with('success', 'Form submitted successfully!');
    }

    public function update_via_post(Request $request, $id)
    {
        $accounts = Account::findOrFail($id);
    
        $originalData = $accounts->getOriginal();
        $updatedData = $request->all();
    
        // Compare original data with updated data and only update the fields that have changed
        foreach ($updatedData as $key => $value) {
            if ($originalData[$key] != $value) {
                $accounts->$key = $value;
            }
        }
    
        // Save the updated form
        $accounts->save();
    
        return response()->json(['message' => 'Resource updated successfully']);
    }

}
