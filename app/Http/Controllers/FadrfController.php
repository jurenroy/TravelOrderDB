<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FadrfForm;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;

class FadrfController extends Controller
{
    public function index(Request $request)
    {
        $limit = min($request->input('limit', 10), 10000); 
        return response()->json(FadrfForm::orderBy('created_at', 'desc')
        ->limit($limit)
        ->get());
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

        if ($request->has('documents')) {
            $FadrfForm->documents = json_encode($request->documents); // Store all fields, not just name
        }
        if ($request->has('remarks')){
            $FadrfForm->remarks = $request->remarks;
        }

        if ($request->has('note')){
            $FadrfForm->note = $request->note;
        }

        $oldValues = $FadrfForm->toArray();

        $FadrfForm->save();

        // Audit log
        AuditLog::create([
            'model' => 'FadrfForm',
            'model_id' => $FadrfForm->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Send notification via websocket
        $this->sendNotification('FAD RF Form Updated', 'FAD RF form has been updated.');

        return response()->json($FadrfForm);

    } catch (\Exception $e){
        \Log::error('Error updating request: '.$e->getMessage());

        return response()->json(['error'=> 'Internal Server Error', 'message'=> $e->getMessage()], 500);
    }
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
