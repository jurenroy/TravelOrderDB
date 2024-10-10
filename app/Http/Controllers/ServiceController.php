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
            'remarks' => 'nullable|string', // Allow remarks but set to blank
            'requestedBy' => 'required|integer',
            'approvedBy' => 'nullable|integer',
            'servicedBy' => 'nullable|integer',
            'feedback_filled' => 'boolean',
        ]);

        // Create the service with blank serviceRequestNo
        $service = Service::create(array_merge($request->all(), [
            'serviceRequestNo' => 'TBA' // Set serviceRequestNo to null on creation
        ]));
        
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
            'remarks' => 'sometimes|nullable|string', // Validate remarks on update
            'requestedBy' => 'sometimes|required|integer',
            'approvedBy' => 'sometimes|nullable|integer',
            'servicedBy' => 'sometimes|nullable|integer',
            'feedback_filled' => 'sometimes|boolean',
        ]);

        // Only set serviceRequestNo if remarks is 'Done'
        if ($request->remarks === 'Done') {
            // Get the current year and month
            $currentYear = date('Y');
            $currentMonth = date('m');

            // Find the last service request number in the current month and year
            $lastService = Service::whereYear('created_at', $currentYear)
                                  ->whereMonth('created_at', $currentMonth)
                                  ->orderBy('id', 'desc')
                                  ->first();

            // Determine the next request number
            if ($lastService) {
                // Extract the last numeric part of the serviceRequestNo
                $lastRequestNo = $lastService->serviceRequestNo;
                $lastId = intval(substr($lastRequestNo, strrpos($lastRequestNo, '-') + 1));
                $nextId = $lastId + 1;
            } else {
                $nextId = 1; // ID starts at 1 if no previous "done" requests
            }
            
            // Format the new service request number without leading zeros
            $serviceRequestNo = "$currentYear-$currentMonth-$nextId";

            $service->serviceRequestNo = $serviceRequestNo; // Set the serviceRequestNo
        }

        // Update the service with the new values
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