<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Document;
use App\Models\Field;
use App\Models\DocumentData;
use Illuminate\Support\Facades\Log;

class MSDSApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_document_data()
    {
        // Create a field to be used for document data
        $field = Field::factory()->create();

        // Prepare data for creating a document
        $documentData = [
            'file_name' => 'test_msds.pdf',
            'fields' => [
                [
                    'field_name' => $field->field_name,
                    'value' => 'Some value',
                    'confidence' => 90,
                ],
            ],
        ];


        // Perform the POST request to create the document data
        $response = $this->postJson('/api/documents', $documentData);

        Log::info('Test response (test_create_document_data):', [
            'response' => $response->json(),
        ]);

        // Assert the response is successful
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'file_name',
                'created_at',
                'updated_at',
                'document_data' => [
                    '*' => [
                        'id',
                        'field_id',
                        'document_id',
                        'value',
                        'confidence',
                    ],
                ],
            ]);

        // Assert the document exists in the database
        $this->assertDatabaseHas('documents', [
            'file_name' => 'test_msds.pdf',
        ]);
    }



    public function test_fetch_all_documents()
    {
        // Create a few MSDS documents using factories
        $documents = \App\Models\Document::factory()->count(3)->create();

        // Make a GET request to retrieve all documents
        $response = $this->getJson('/api/documents');

        Log::info('Test response (test_fetch_all_documents):', [
            'response' => $response->json(),
        ]);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_fetch_single_document()
    {
        // Create a single document
        $document = Document::factory()->create();

        // Make GET request to retrieve the document
        $response = $this->getJson("/api/documents/{$document->id}");

        Log::info('Test response (test_fetch_single_document):', [
            'response' => $response->json(),
        ]);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'id' => $document->id,
                'file_name' => $document->file_name,
            ]);
    }

    public function test_update_document()
    {
        // Create a document and a field
        $document = Document::factory()->create();
        $field = Field::factory()->create();

        // Prepare updated data for the document
        $updatedData = [
            'file_name' => 'updated_msds.pdf',
            'fields' => [
                [
                    'field_id' => $field->id,
                    'value' => 'Updated value',
                    'confidence' => 95,
                ],
            ],
        ];

        // Perform PUT request to update the document
        $response = $this->putJson("/api/documents/{$document->id}", $updatedData);

        Log::info('Test response (test_update_document):', [
            'response' => $response->json(),
        ]);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'file_name' => 'updated_msds.pdf',
            ]);

        // Assert that the document was updated in the database
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'file_name' => 'updated_msds.pdf',
        ]);
    }

    public function test_delete_document()
    {
        // Create a document
        $document = Document::factory()->create();

        // Perform DELETE request to remove the document
        $response = $this->deleteJson("/api/documents/{$document->id}");

        Log::info('Test response (test_delete_document):', [
            'response' => $response->json(),
        ]);

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert that the document no longer exists in the database
        $this->assertDatabaseMissing('documents', [
            'id' => $document->id,
        ]);
    }
}
