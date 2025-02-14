<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestForm;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        return response()->json(RequestForm::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_id' => 'required|integer',
            'division_id' => 'required|integer',
            'date' => 'required|date',
            'documents' => 'required|array',
            'rating' => 'nullable|integer',
        ]);

        $validatedData['documents'] = json_encode($validatedData['documents']);

        // Create a new leave form record with the validated data
        $leaveForm = RequestForm::create($validatedData);

        return response()->json($leaveForm, 201);
    }

    public function show($id)
    {

        $request = RequestForm::find($id);
        if (!$request) {
            return response()->json(['message' => 'request not found'], 404);
        }
        return response()->json(RequestForm::all());

    }

    public function update(Request $request, $id)
    {
        // Validate only the fields we want to update
        $request->validate([
            'rating' => 'nullable|integer',
        ]);

        // Find the request form by ID
        $RequestForm = RequestForm::findOrFail($id);

        // Prepare the data to update
        $dataToUpdate = $request->only(['rating']);

        // Update the request form with the new data
        $RequestForm->update($dataToUpdate);

        // Return the updated request form as a JSON response
        return response()->json($RequestForm);
    }
}
