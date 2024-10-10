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

            // Count how many services exist for the current month and year
            $count = Service::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('serviceRequestNo', '!=', 'TBA') // Only count those not 'TBA'
            ->count();

            // Determine the next request number
            $nextId = $count > 0 ? $count + 1 : 1; // Increment if count exists, else start at 1

            // Format the new service request number
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