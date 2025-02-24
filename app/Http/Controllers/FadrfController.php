<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FadrfForm;
use Illuminate\Http\Request;

class FadrfController extends Controller
{
    public function index()
    {
        return response()->json(FadrfForm::all());
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_id'=> 'required|integer',
            'division_id'=> 'required|integer',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'documents'=> 'required|array',
            'rating'=> 'nullable|integer',
            'remarks'=> 'nullable|string',
            'note'=> 'nullable|string',
        ]);

        $validatedData['documents'] = json_encode(array_map(function($doc) {
            return is_array($doc) ? ($doc['name'] ?? $doc) : $doc;
        }, $validatedData['documents']));
         
       
        $leaveForm = FadrfForm::create($validatedData);
        return response()->json($leaveForm,  201);
    }
    public function show($id)
    {
        $request = FadrfForm::find($id);
        if (!$request){
            return response()->json(['message'=> 'request not found'],404);
        }
        return response()->json($request);
    }
    public function update(Request $request, $id)
    {
        try{
        $request->validate([
            'rating'=>'nullable|integer',
            'documents'=> 'nullable|array',
            'remarks'=> 'nullable|string',
            'note'=> 'nullable|string',
        ]);

        $FadrfForm = FadrfForm::findOrFail($id);
        if ($request->has('rating')){
            $FadrfForm->rating = $request->rating;
        }

        if ($request->has('documents')){
            $FadrfForm->documents = json_encode(array_map(function($doc) {
                return is_array($doc) ? ($doc['name'] ?? $doc) : $doc;
            }, $request->documents));
        }

        if ($request->has('remarks')){
            $FadrfForm->remarks = $request->remarks;
        }

        if ($request->has('note')){
            $FadrfForm->note = $request->note;
        }

        $FadrfForm->save();


        return response()->json($FadrfForm);

    } catch (\Exception $e){
        \Log::error('Error updating request: '.$e->getMessage());

        return response()->json(['error'=> 'Internal Server Error', 'message'=> $e->getMessage()], 500);
    }
}
}
