<?php

namespace App\Http\Controllers;

use App\Models\DocumentData;
use Illuminate\Http\Request;

class DocumentDataController extends Controller
{
    // Retrieve all document data
    public function index()
    {
        $documentData = DocumentData::all();
        return response()->json($documentData);
    }
    

    // Retrieve specific data related to a document by document ID
    public function show($documentId)
    {
        $documentData = DocumentData::where('document_id', $documentId)->get();
        if ($documentData->isEmpty()) {
            return response()->json(['message' => 'No data found for this document'], 404);
        }
        return response()->json($documentData);
    }
}
