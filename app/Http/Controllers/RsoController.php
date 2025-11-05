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
        $query = Rso::with('signatory');

        // Get authenticated user
        $userId = auth()->check() ? auth()->user()->id : null;

        // Restriction: users with id 76 or 24 can see all RSOs, others only their own
        if ($userId && !in_array($userId, [76, 24])) {
            $query->where('rso_name', 'like', '%' . $userId . '%');
        }

        // Filters based on user type
        if (in_array($userId, [76, 24])) {
            // For users 76/24: only search filter (name or RSO number)
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('rso_number', 'like', '%' . $search . '%')
                      ->orWhere('rso_name', 'like', '%' . $search . '%');
                });
            }
        } else {
            // For other users: only date filter
            if ($request->has('scheduled_date') && !empty($request->scheduled_date)) {
                $date = $request->scheduled_date;
                $query->where('rso_scheduled_dates_from', '<=', $date)
                      ->where('rso_scheduled_dates_to', '>=', $date);
            }
        }

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

