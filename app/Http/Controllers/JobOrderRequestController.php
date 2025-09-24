<?php

namespace App\Http\Controllers;

use App\Models\JobOrderRequest;
use Illuminate\Http\Request;

class JobOrderRequestController extends Controller
{
    // Store new job order
    public function store(Request $request)
    {
        $data = $request->all();
        $jobOrder = JobOrderRequest::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Job Order Request created successfully.',
            'data' => $jobOrder
        ]);
    }

    // Get all job orders
    public function getAll(Request $request)
    {
        $jobOrders = JobOrderRequest::all();

        return response()->json([
            'success' => true,
            'data' => $jobOrders
        ]);
    }

    // Get one job order by ID
    public function getOne(Request $request)
    {
        $jobOrder = JobOrderRequest::find($request->id);

        if (!$jobOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Job Order Request not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $jobOrder
        ]);
    }

    // Update job order
    public function update(Request $request)
    {
        $jobOrder = JobOrderRequest::find($request->id);

        if (!$jobOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Job Order Request not found.'
            ], 404);
        }

        $jobOrder->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Job Order Request updated successfully.',
            'data' => $jobOrder
        ]);
    }
}
