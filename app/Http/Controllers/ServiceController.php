<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{

    public function showService($name_id, $status, $typeOfService, $limit, $offset, $countOnly = false)
{
    // Initialize the query
    $query = Service::query();

    // Apply conditions based on the name_id
    if (in_array($name_id, [76, 77, 53])) {
        // Admins can see all services
        if ($status !== 'all') {
            if ($status === 'pending') {
                $query->where('serviceRequestNo', 'TBA');
            } elseif ($status === 'approved') {
                $query->whereNotNull('approvedBy');
            } elseif ($status === 'disapproved') {
                $query->whereNotNull('approvedBy')->where('remarks', 'Disapproved');
            } elseif ($status === 'ongoing') {
                $query->where('remarks', 'On-going');
            } elseif ($status === 'done') {
                $query->where('remarks', 'Done');
            } elseif ($status === 'kulang') {
                $query->where('serviceRequestNo', 'TBA');
            }
        }

        // Filter by typeOfService if not 'All'
        if ($typeOfService !== 'all') {
            $query->where('typeOfService', 'like', '%' . $typeOfService . '%');
        }
    } elseif (in_array($name_id, [66])) {
        // Specific conditions for approvedBy null
        if ($status !== 'all') {
            if ($status === 'pending') {
                $query->whereNull('approvedBy');
            } elseif ($status === 'approved') {
                $query->whereNotNull('approvedBy');
            } elseif ($status === 'disapproved') {
                $query->whereNotNull('approvedBy')->where('remarks', 'Disapproved');
            } elseif ($status === 'ongoing') {
                $query->where('remarks', 'On-going');
            } elseif ($status === 'done') {
                $query->where('remarks', 'Done');
            } elseif ($status === 'kulang') {
                $query->whereNull('approvedBy');
            }
        }
    } else {
        // For users who are not admins, filter based on requestedBy
        $query->where('requestedBy', $name_id);
        if ($status !== 'all') {
            // Start the query with the requestedBy condition

            if ($status === 'pending') {
                $query->where('serviceRequestNo', 'TBA')
                      ->orWhereNull('approvedBy')
                      ->where('requestedBy', $name_id)
                      ->orWhere('feedback_filled', 0)
                      ->where('requestedBy', $name_id);
            } elseif ($status === 'approved') {
                $query->whereNotNull('approvedBy');
            } elseif ($status === 'disapproved') {
                $query->whereNotNull('approvedBy')->where('remarks', 'Disapproved');
            } elseif ($status === 'ongoing') {
                $query->where('remarks', 'On-going');
            } elseif ($status === 'done') {
                $query->where('remarks', 'Done');
            } elseif ($status === 'kulang') {
                $query->where('serviceRequestNo', 'TBA')
                      ->orWhereNull('approvedBy')
                      ->where('requestedBy', $name_id)
                      ->orWhere('feedback_filled', 0)
                      ->where('requestedBy', $name_id);
            }
        }
    }

    // Filter by typeOfService if not 'All'
    if ($typeOfService !== 'all') {
        $query->where('typeOfService', 'like', '%' . $typeOfService . '%');
    }

    // If we only need the count, return it directly
    if ($countOnly) {
        return $query->count();
    }

    // Limit the number of rows returned and order by ID in descending order
    $feedbacks = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

    // // Check if any feedbacks were found
    // if ($feedbacks->isEmpty()) {
    //     return response()->json(['message' => 'No feedback found'], 404);
    // }

    return response()->json($feedbacks);
}

public function getCount($name_id)
{
    // Call the getForm method with 'Pending' status and only ask for the count
    return $this->showService($name_id, 'kulang', 'all', 0, 0, true);
}
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
        'ictnote' => 'nullable|string',
        'files.*' => 'nullable|file', // No MIME type or size restriction
    ]);

    // Handle file upload (if any)
    $fileNames = [];

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            // Get the file name (not the full path)
            $fileName = $file->getClientOriginalName(); // You can use a unique name if needed

            // Store the file in the 'ictrequest' folder within public storage
            $file->storeAs('ictrequest', $fileName, 'public'); // Store in 'storage/app/public/ictrequest'

            // Add the file name to the array
            $fileNames[] = $fileName;
        }
    }
    // return $fileNames;

    // // Convert the array of file names into a string in the desired format
    // $fileNamesString = '[' . implode(', ', array_map(function($fileName) {
    //     return "\"$fileName\""; // Add double quotes around each file name
    // }, $fileNames)) . ']';
    

    // Create the service with blank serviceRequestNo and the uploaded files
    $service = Service::create(array_merge($request->all(), [
        'serviceRequestNo' => 'TBA', // Default value for service request number
        'files' => !empty($fileNames) ? $fileNames : null, // Store the formatted string
    ]));
    // // Return the service with the file field
    // return response()->json([
    //     'service' => $service,
    //     'files' => json_decode($service->file) // Decode JSON if it's stored as JSON
    // ]);
    
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
        'ictnote' => 'sometimes|nullable|string',
        'files.*' => 'nullable|file', // No MIME type or size restriction
    ]);

    // Handle file upload (if any)
    $filePaths = [];

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            // Store the file in the 'ictrequest' folder within public storage
            $filePath = $file->store('ictrequest', 'public'); // Store in 'storage/app/public/ictrequest'

            // Add the file path to the array (so we can store it in the database)
            $filePaths[] = $filePath;
        }

        // Add the new files to the existing ones (if any)
        if ($service->file) {
            $existingFiles = explode(',', $service->file); // Split the existing file paths into an array
            $filePaths = array_merge($existingFiles, $filePaths); // Merge the new files with the existing ones
        }

        // Store the updated file paths in the database
        $service->file = implode(',', $filePaths); // Convert array to string before saving
    }

    // Only set serviceRequestNo if remarks is 'Done'
    if ($request->remarks === 'Done') {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $count = Service::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('serviceRequestNo', '!=', 'TBA')
            ->count();

        $nextId = $count > 0 ? $count + 1 : 1;
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