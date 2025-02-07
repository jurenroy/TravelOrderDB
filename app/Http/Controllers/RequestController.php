<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\requestForm;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        return response()->json(requestForm::all());
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
        $leaveForm = requestForm::create($validatedData);

        return response()->json($leaveForm, 201);
    }

    public function show($id)
    {

        $request = requestForm::find($id);
        if (!$request) {
            return response()->json(['message' => 'request not found'], 404);
        }
        return response()->json($request);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_id' => 'nullable|integer',
            'division_id' => 'nullable|integer',
            'date' => 'nullable|date',
            'documents' => 'nullable|array',
            'rating' => 'nullable|integer',
        ]);

        $requestForm = requestForm::findOrFail($id);
        $requestForm->update($request->all());
        return $requestForm;
    }
}
