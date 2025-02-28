<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Name;

class NameController extends Controller
{
    public function show($name_id)
    {
        try {
            // Find the name by name_id
            $name = Name::where('name_id', $name_id)->firstOrFail();

            // Return the name as a JSON response
            return response()->json($name);
        } catch (ModelNotFoundException $e) {
            // Return a 404 response if the name is not found
            return response()->json(['error' => 'Name not found'], 404);
        }
    }
}