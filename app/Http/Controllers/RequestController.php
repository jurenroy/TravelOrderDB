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
            'remarks' => 'nullable|string',
        ]);

        $validatedData['documents'] = json_encode(array_map(function($doc) {
            return is_array($doc) ? ($doc['name'] ?? $doc) : $doc;
        }, $validatedData['documents']));

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
    try {
        $request->validate([
            'rating' => 'nullable|integer',
            'documents' => 'nullable|array',
            'remarks' => 'nullable|string', 
        ]);

        $requestForm = RequestForm::findOrFail($id);
        if ($request->has('rating')) {
            $requestForm->rating = $request->rating;
        }

        if ($request->has('documents')) {
            $requestForm->documents = json_encode(array_map(function($doc) {
                return is_array($doc) ? ($doc['name'] ?? $doc) : $doc;
            }, $request->documents));
        }

        // Update remarks if provided
        if ($request->has('remarks')) {
            $requestForm->remarks = $request->remarks; 
        }

        // Save the updated request form
        $requestForm->save();

        // Return the updated request form as a JSON response
        return response()->json($requestForm);
        
    } catch (\Exception $e) {
        // Log the error message
        \Log::error('Error updating request: ' . $e->getMessage());

        // Return a 500 error response with the error message
        return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    }
}
}
