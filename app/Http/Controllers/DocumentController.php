<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentData;
use App\Models\Field;
use Illuminate\Http\Request;
use App\Services\LogicAppService;
use App\Http\Requests\TriggerLogicAppRequest;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    // Get a list of all documents
    public function index(Request $request)
    {
        $query = Document::with('documentData.field');

        // Searching
        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->whereHas('documentData', function ($q) use ($search) {
                $q->whereHas('field', function ($fieldQuery) use ($search) {
                    $fieldQuery->where('field_name', 'Trade name')
                        ->where('value', 'like', "%{$search}%");
                });
            });
        }

        // Date Filtering
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Sorting
        if ($request->has('sort_column') && $request->has('sort_direction')) {
            $validColumns = ['file_name', 'created_at', 'updated_at'];
            $sortColumn = in_array($request->sort_column, $validColumns) ? $request->sort_column : 'created_at';
            $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginate the results
        return $query->paginate(50);
    }



    // Get a specific document
    public function show($id)
    {
        // $document = Document::findOrFail($id);
        // Load the document with related document data and fields
        $document = Document::with('documentData.field')->findOrFail($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        return response()->json($document);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'file_name' => 'required|string',
            'fields' => 'required|array',
            'fields.*.field_name' => 'required|string',
            'fields.*.value' => 'required|string',
            'fields.*.confidence' => 'required|numeric|between:0,100',
        ]);

        // Create the document
        $document = Document::create([
            'file_name' => $validatedData['file_name'],
        ]);

        // Map field_name to field_id dynamically
        foreach ($validatedData['fields'] as $fieldData) {
            $field = Field::where('field_name', $fieldData['field_name'])->first();

            if ($field) {
                DocumentData::create([
                    'document_id' => $document->id,
                    'field_id' => $field->id,
                    'value' => $fieldData['value'],
                    'confidence' => $fieldData['confidence'],
                ]);
            }
        }

        // Return the created document with related document data
        return response()->json($document->load('documentData.field'), 201);
    }

    // Update an existing document
    public function update(Request $request, string $id)
    {
        $document = Document::findOrFail($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        // Validate the update request data
        $validatedData = $request->validate([
            'file_name' => 'sometimes|required|string',
            'fields' => 'sometimes|required|array',
            'fields.*.field_id' => 'required|integer|exists:fields,id',
            'fields.*.value' => 'required|string',
            'fields.*.confidence' => 'required|numeric|between:0,100',
        ]);

        // Update the document details
        $document->update($validatedData);

        // If fields are provided, update document data
        if (isset($validatedData['fields'])) {
            // Delete existing document data and recreate it
            DocumentData::where('document_id', $document->id)->delete();

            foreach ($validatedData['fields'] as $fieldData) {
                DocumentData::create([
                    'document_id' => $document->id,
                    'field_id' => $fieldData['field_id'],
                    'value' => $fieldData['value'],
                    'confidence' => $fieldData['confidence'],
                ]);
            }
        }

        // Return the updated document along with its related data
        return response()->json($document->load('documentData.field'));
    }

    // Delete a document
    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        // Delete the document
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function triggerLogicApp(Request $request, LogicAppService $logicAppService)
    {
        try {
            // Ensure the uploaded file exists
            if (!$request->hasFile('file_content')) {
                throw new \Exception('No file uploaded.');
            }

            // Retrieve the file
            $file = $request->file('file_content');

            // Get the file content and encode it as base64
            $fileContent = base64_encode(file_get_contents($file->getRealPath()));

            // Extract the file name
            $fileName = $file->getClientOriginalName();

            // Trigger the Logic App
            $response = $logicAppService->triggerWorkflow($fileName, $fileContent);

            return response()->json(['message' => 'Workflow triggered successfully', 'response' => $response], 200);
        } catch (\Exception $e) {
            Log::error('Error triggering Logic App', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to trigger Logic App', 'error' => $e->getMessage()], 500);
        }
    }
}
