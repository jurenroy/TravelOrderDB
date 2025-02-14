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
        ]);

        $validatedData['documents'] = json_encode($validatedData['documents']);
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
        $request->validate([
            'rating'=>'nullable|integer'
        ]);

        $FadrfForm = FadrfForm::findOrFail($id);

        $dataToUpdate = $request->only(['rating']);

        $FadrfForm->update($dataToUpdate);

        return response()->json($FadrfForm);
    }

}
