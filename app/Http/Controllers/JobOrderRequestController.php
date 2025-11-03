<?php

namespace App\Http\Controllers;

use App\Models\JobOrderRequest;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;

class JobOrderRequestController extends Controller
{
    // Store new job order
    public function store(Request $request)
    {
        $data = $request->all();
        $jobOrder = JobOrderRequest::create($data);

        // Audit log
        AuditLog::create([
            'model' => 'JobOrderRequest',
            'model_id' => $jobOrder->id,
            'action' => 'created',
            'new_values' => $data,
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Job Order Request Created', 'A new job order request has been created.');

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

        $oldValues = $jobOrder->toArray();
        $jobOrder->update($request->all());

        // Audit log
        AuditLog::create([
            'model' => 'JobOrderRequest',
            'model_id' => $jobOrder->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('Job Order Request Updated', 'Job order request has been updated.');

        return response()->json([
            'success' => true,
            'message' => 'Job Order Request updated successfully.',
            'data' => $jobOrder
        ]);
    }

    private function sendNotification($title, $message)
    {
        // Send HTTP request to Django websocket server to broadcast notification
        // Assuming Django is running on 202.137.117.84:8012
        try {
            Http::post('http://202.137.117.84:8012/api/send-notification/', [
                'title' => $title,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            // Log error if notification fails
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }
}
