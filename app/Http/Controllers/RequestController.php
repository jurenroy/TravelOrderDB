<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestForm;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request) {
        $limit = min($request->input('limit', 10), 10000);
        $nameId = $request->input('name_id'); 
        $query = RequestForm::orderBy('created_at', 'desc');


        if ($nameId) {
            if ($nameId == "2" || $nameId == "76") {
               
            } else {
                
                $query->where('name_id', $nameId);
            }
        }
    
        return response()->json($query->limit($limit)->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_id' => 'required|integer',
            'division_id' => 'required|integer',
            'date' => 'required|date',
            'documents' => 'required|array',
            'rating' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

       
        $validatedData['documents'] = json_encode($validatedData['documents']);

        
        $requestForm = RequestForm::create($validatedData);

        return response()->json($requestForm, 201);
    }

    public function show($id)
    {

        $request = RequestForm::find($id);
        if (!$request) {
            return response()->json(['message' => 'request not found'], 404);
        }
        return response()->json(RequestForm::all());

    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'rating' => 'nullable|integer',
                'documents' => 'nullable|array',
                'remarks' => 'nullable|string',
                'note' => 'nullable|string',
            ]);

            $requestForm = RequestForm::findOrFail($id);

            $userId = auth()->id();

            // Check if the user is allowed to rate the request
            if (($userId == 2 || $userId == 76) && $userId !== $requestForm->name_id) {
                return response()->json([
                    'error' => 'You can only rate your own request.'
                ], 403);
            }

            if ($request->has('rating')) {
                $requestForm->rating = $request->rating;
            }

            // Keep full documents (with remarks)
            if ($request->has('documents')) {
                $requestForm->documents = json_encode($request->documents);
            }

            if ($request->has('remarks')) {
                $requestForm->remarks = $request->remarks;
            }

            if ($request->has('note')) {
                $requestForm->note = $request->note;
            }

            
            $requestForm->save();

            return response()->json($requestForm);

        } catch (\Exception $e) {
            \Log::error('Error updating request: ' . $e->getMessage());

            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

}
