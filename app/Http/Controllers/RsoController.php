<?php

namespace App\Http\Controllers;

use App\Models\Rso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Smalot\PdfParser\Parser;

class RsoController extends Controller
{
    /**
     * Display a listing of the RSO records.
     */


     public function query($search)
{
    $results = Rso::where(function($q) use ($search) {

        // 1️⃣ Partial match in rso_number (comma-separated string)
        $q->orWhere('rso_number', 'like', '%' . $search . '%');

        // 2️⃣ Partial match in rso_name
        $q->orWhere('rso_name', 'like', '%' . $search . '%');

        // 3️⃣ Check if search is a date and is inside scheduled date range
        if (strtotime($search) !== false) {
            $q->orWhere(function($q2) use ($search) {
                $q2->where('rso_scheduled_dates_from', '<=', $search)
                   ->where('rso_scheduled_dates_to', '>=', $search);
            });
        }

    })->with('signatory')->get();

    if ($results->isEmpty()) {
        return response()->json(['message' => 'No match found'], 404);
    }

    return response()->json($results);
}

public function index(Request $request)
{
    // Initialize the query for RSOs
    $query = Rso::with('signatory');

    // Get the 'name' parameter from the request
    $name = $request->name;

    // Convert the 'name' parameter to an array (explode by comma if it's a comma-separated string)
    $nameArray = explode(',', $name); // Example: '6' => ['6'], '6,12' => ['6', '12']

    // If the name is "76" or "24", show all RSOs (admins)
    if (in_array($name, ['76', '24'])) {
        // Admins (76, 24) can see all RSOs
        // No additional filtering needed here
    } else {
        // Normal users: Filter RSOs based on the `rso_name` field
        $query->where(function ($q) use ($nameArray) {
            $q->where('rso_name', 'like', '%all%')  // Allow access to "all"
              ->orWhere(function($q2) use ($nameArray) {
                  // Check if any of the name_ids from $nameArray are contained in rso_name
                  foreach ($nameArray as $nameId) {
                      $q2->orWhereRaw("FIND_IN_SET(?, rso_name)", [$nameId]);
                  }
              });
        });
    }

    // Now, apply search if provided
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;

        // Check if searchTerm is numeric (rso_number search) or a string (name search)
        if (is_numeric($searchTerm)) {
            // If it's numeric, search by rso_number
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("REPLACE(rso_number, 'No. ', '') LIKE ?", ['%' . $searchTerm . '%']);
            });
        } else if (strpos($searchTerm, '-') !== false) {
            // If the search term contains a dash, assume it's part of the rso_number
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("REPLACE(rso_number, 'No. ', '') LIKE ?", ['%' . $searchTerm . '%']);
            });
        }else {
            // If it's a string, search by the Names model
            $names = \App\Models\Name::where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('middle_init', 'like', '%' . $searchTerm . '%')
                      ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
            })->get();

            // Get all matching name_ids from the Names model
            $nameIds = $names->pluck('name_id')->toArray();

            // If we found matching name_ids, filter RSOs accordingly
            if (count($nameIds) > 0) {
                $query->where(function ($q) use ($nameIds) {
                    // Check if rso_name is "all" or matches any name_id from the array
                    $q->where('rso_name', 'like', '%all%')  // Allow access to "all"
                      ->orWhere(function($q2) use ($nameIds) {
                          // Treat rso_name as an array for comparison
                          foreach ($nameIds as $nameId) {
                              $q2->orWhereRaw("FIND_IN_SET(?, rso_name)", [$nameId]);
                          }
                      });
                });
            } else {
                // If no names match, return empty or handle as needed
                return response()->json([]);
            }
        }
    }

    // Return the filtered result as a JSON response
    return response()->json($query->get());
}



    /**
     * Store a newly created RSO record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rso_number' => 'required|string|unique:rsos,rso_number',
            'rso_name' => 'required|string|max:255',
            'rso_date' => 'required|date',
            'rso_subject' => 'required|string',
            'rso_scheduled_dates_from' => 'nullable|date',
            'rso_scheduled_dates_to' => 'nullable|date',
            'rso_signatory' => 'nullable|exists:users,id',
            'rso_remarks' => 'nullable|string',
            'rso_scan_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('rso_scan_copy')) {
            $file = $request->file('rso_scan_copy');
            $filename = 'RSO_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/RSO', $filename);
            $validated['rso_scan_copy'] = $filename;
        }

        $rso = Rso::create($validated);

        return response()->json($rso, 201);
    }

    /**
     * Display the specified RSO record.
     */
    public function show($rso_number)
    {
        $rso = Rso::with('signatory')->findOrFail($rso_number);
        return response()->json($rso);
    }

    /**
     * Update the specified RSO record in storage.
     */
    public function update(Request $request, $rso_number)
    {
        $rso = Rso::findOrFail($rso_number);

        $validated = $request->validate([
            'rso_name' => 'sometimes|string|max:255',
            'rso_date' => 'sometimes|date',
            'rso_subject' => 'sometimes|string',
            'rso_scheduled_dates_from' => 'nullable|date',
            'rso_scheduled_dates_to' => 'nullable|date',
            'rso_signatory' => 'sometimes|exists:users,id',
            'rso_remarks' => 'nullable|string',
            'rso_scan_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('rso_scan_copy')) {
            if ($rso->rso_scan_copy && Storage::exists('public/RSO/' . $rso->rso_scan_copy)) {
                Storage::delete('public/RSO/' . $rso->rso_scan_copy);
            }

            $file = $request->file('rso_scan_copy');
            $filename = 'RSO_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/RSO', $filename);
            $validated['rso_scan_copy'] = $filename;
        }

        $rso->update($validated);

        return response()->json($rso);
    }

    /**
     * Get current authenticated user.
     */
    public function user()
    {
        return response()->json(auth()->user());
    }

    /**
     * Remove the specified RSO record from storage.
     */
    public function destroy($rso_number)
    {
        $rso = Rso::findOrFail($rso_number);

        if ($rso->rso_scan_copy && Storage::exists('public/RSO/' . $rso->rso_scan_copy)) {
            Storage::delete('public/RSO/' . $rso->rso_scan_copy);
        }

        $rso->delete();

        return response()->json(['message' => 'RSO deleted successfully']);
    }

}

