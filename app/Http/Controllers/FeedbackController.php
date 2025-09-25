<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Service;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Retrieve all feedbacks
    public function index()
    {
        return response()->json(Feedback::all());
    }

    // Create a new feedback
    public function store(Request $request)
    {
        $request->validate([
            'referenceid' => 'required|exists:services,id',
            'evaluation1' => 'required|integer|min:1|max:5',
            'evaluation2' => 'required|integer|min:1|max:5',
            'evaluation3' => 'required|integer|min:1|max:5',
            'evaluation4' => 'required|integer|min:1|max:5',
            'date' => 'nullable|date',
        ]);

        $feedback = Feedback::create($request->all());

        // Update the service's feedback_filled status
        $service = Service::find($request->referenceid);
        if ($service) {
            $service->feedback_filled = true;
            $service->save();
        }
        return response()->json($feedback, 201);
    }

    // Retrieve feedback based on id, but query by referenceid
    public function show($id)
    {
        // Find feedback where referenceid matches the id parameter
        $feedback = Feedback::where('referenceid', $id)->first();
        
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found for the given referenceid'], 404);
        }
        
        return response()->json($feedback);
    }

    // Update a specific feedback
    public function update(Request $request, $id)
    {
        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }

        $request->validate([
            'referenceid' => 'sometimes|required|exists:services,id',
            'evaluation1' => 'sometimes|required|integer|min:1|max:5',
            'evaluation2' => 'sometimes|required|integer|min:1|max:5',
            'evaluation3' => 'sometimes|required|integer|min:1|max:5',
            'evaluation4' => 'sometimes|required|integer|min:1|max:5',
            'date' => 'sometimes|nullable|date',
        ]);

        $feedback->update($request->all());
        return response()->json($feedback);
    }

    // Delete a specific feedback
    public function destroy($id)
    {
        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }
        $feedback->delete();
        return response()->json(['message' => 'Feedback deleted successfully']);
    }
}
