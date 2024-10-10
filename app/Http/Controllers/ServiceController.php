<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    // Retrieve all services
    public function index()
    {
        return response()->json(Service::all());
    }

    // Create a new service
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'division_id' => 'required|integer',
            'typeOfService' => 'required|string',
            'note' => 'nullable|string',
            'remarks' => 'nullable|string',
            'requestedBy' => 'required|integer',
            'approvedBy' => 'nullable|integer',
            'servicedBy' => 'nullable|integer',
            'feedback_filled' => 'boolean',
        ]);

        // Get the current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Find the last service request number in the current month and year
        $lastService = Service::whereYear('created_at', $currentYear)
                              ->whereMonth('created_at', $currentMonth)
                              ->orderBy('id', 'desc')
                              ->first();
        
        // Determine the next request number
        $nextId = $lastService ? intval(substr($lastService->serviceRequestNo, -1)) + 1 : 1; // ID starts at 1
        
        // Format the new service request number without leading zeros
        $serviceRequestNo = "$currentYear-$currentMonth-$nextId";
        
        // Create the service with the new request number
        $service = Service::create(array_merge($request->all(), ['serviceRequestNo' => $serviceRequestNo]));
        
        return response()->json($service, 201);
    }

    // Retrieve a specific service
    public function show($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    // Update a specific service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $request->validate([
            'date' => 'sometimes|nullable|date',
            'division_id' => 'sometimes|required|integer',
            'typeOfService' => 'sometimes|required|string',
            'note' => 'sometimes|nullable|string',
            'remarks' => 'sometimes|nullable|string',
            'requestedBy' => 'sometimes|required|integer',
            'approvedBy' => 'sometimes|nullable|integer',
            'servicedBy' => 'sometimes|nullable|integer',
            'feedback_filled' => 'sometimes|boolean',
        ]);

        $service->update($request->all());
        return response()->json($service);
    }

    // Delete a specific service
    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }
}