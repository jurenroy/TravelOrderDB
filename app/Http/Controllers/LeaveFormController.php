<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;

class LeaveFormController extends Controller
{
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
            'tevl' => 'nullable|integer',
            'tesl' => 'nullable|integer',
            'ltavl' => 'nullable|integer',
            'ltasl' => 'nullable|integer',
            'bvl' => 'nullable|integer',
            'vsl' => 'nullable|integer',
            'certification' => 'nullable|string',
            'reco' => 'nullable|string',
            'recodesc' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'dayswpay' => 'nullable|integer',
            'dayswopay' => 'nullable|integer',
            'others' => 'nullable|string',
            'disapproved' => 'nullable|string',
            'approval' => 'nullable|string',
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
            'days' => 'nullable|integer',
            'dates' => 'nullable|date',
            'commutation' => 'nullable|string',
            'applicant' => 'nullable|integer',
            'asof' => 'nullable|string',
            'tevl' => 'nullable|integer',
            'tesl' => 'nullable|integer',
            'ltavl' => 'nullable|integer',
            'ltasl' => 'nullable|integer',
            'bvl' => 'nullable|integer',    
            'vsl' => 'nullable|integer',
            'certification' => 'nullable|string',
            'reco' => 'nullable|string',
            'recodesc' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'dayswpay' => 'nullable|integer',
            'dayswopay' => 'nullable|integer',
            'others' => 'nullable|string',
            'disapproved' => 'nullable|string',
            'approval' => 'nullable|string',
        ]);

        // Find the leave form record by ID and update it with the validated data
        $leaveForm = LeaveForm::findOrFail($id);
        $leaveForm->update($validatedData);

        return response()->json($leaveForm, 200);
    }
}
