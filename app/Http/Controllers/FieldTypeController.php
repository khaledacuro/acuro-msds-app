<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldTypeController extends Controller
{
    // Retrieve all field types
    public function index()
    {
        $fields = Field::paginate(100);
        return response()->json($fields);
    }

    // Retrieve a specific field type
    public function show($id)
    {
        $field = Field::find($id);
        if (!$field) {
            return response()->json(['message' => 'Field not found'], 404);
        }
        return response()->json($field);
    }
}
