<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Employee;

class AccountController extends Controller
{
    public function showForm()
    {
        // If you want to display anything initially when the form is loaded
        // For example, fetching divisions
        $divisions = Account::all();
        return view('account', compact('divisions'));
    }
    public function show($name_id)
    {
        try {
            // Find the name by name_id
            $name = Account::where('name_id', $name_id)->firstOrFail();

            // Return the name as a JSON response
            return response()->json($name);
        } catch (ModelNotFoundException $e) {
            // Return a 404 response if the name is not found
            return response()->json(['error' => 'Name not found'], 404);
        }
    }

    public function submitForm(Request $request)
{
    // Validate form data including signature image
    $validatedData = $request->validate([
        'type_id' => 'required|string',
        'name_id' => 'required|string',
        'email' => 'required|string',
        'password' => 'required|string',
        'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Signature is optional
    ]);

    // Handle signature image upload
    if ($request->hasFile('signature')) {
        // Save the uploaded image to the public/images directory
        $signaturePath = $request->file('signature')->store('images', 'public');
        // Adjust the path to remove the 'public/' prefix
        $validatedData['signature'] = str_replace('public/', '', $signaturePath);
    }

    // Create new form instance
    $account = new Account();

    // Fill the form instance with validated data
    $account->fill($validatedData);

    // If signature path is set, store it in the 'signature' attribute of the model
    if (isset($validatedData['signature'])) {
        $account->signature = $validatedData['signature'];
    }

    // Save the form to the database
    $account->save();

    // Redirect the user back to the form or any other page
    return redirect('/add_account')->with('success', 'Form submitted successfully!');
}


public function update_via_post(Request $request, $id)
{
    $account = Account::findOrFail($id);

    $originalData = $account->getOriginal();
    $updatedData = $request->all();

    // Compare original data with updated data and only update the fields that have changed
    foreach ($updatedData as $key => $value) {
        if ($originalData[$key] != $value) {
            // Special handling for signature field
            if ($key === 'signature' && $request->hasFile('signature')) {
                // Save the uploaded image to the public/images directory
                $signaturePath = $request->file('signature')->store('images', 'public');
                // Adjust the path to remove the 'public/' prefix
                $updatedData[$key] = str_replace('public/', '', $signaturePath);
            }
            $account->$key = $value;
        }
    }

    // If 'signature' field was updated and new path is set, update 'signature' attribute of the model
    if (isset($updatedData['signature'])) {
        $account->signature = $updatedData['signature'];
    }

    // Save the updated form
    $account->save();

    return response()->json(['message' => 'Resource updated successfully']);
}


public function acclogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        // Retrieve the account by email
        $account = Account::where('email', $request->email)->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Email not found'], 404);
        }

        // Retrieve employee with matching name_id
        $employee = Employee::where('name_id', $account->name_id)->first();

        // Check if the account is inactive
        if (!$employee || $employee->isActive === 'out') {
            return response()->json(['success' => false, 'message' => 'Account is inactive or employee not found'], 403);
        }

        return response()->json([
            'password' => 'password',
            'accountId' => $account->account_id,
            'typeId' => $account->type_id,
            'nameId' => $account->name_id,
            'signature' => $account->signature,
        ]);
    }





}
