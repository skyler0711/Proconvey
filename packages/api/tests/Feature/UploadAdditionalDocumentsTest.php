<?php

namespace Tests\Feature;

use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadAdditionalDocumentsTest extends TestCase
{
    private $user;

    private $altUser;

    private $conveyancer;

    private $property;

    private $file_name;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancer = Conveyancer::factory()->create();

        $this->property = Property::factory([
            'conveyancer_id' => $this->conveyancer->id,
        ])->hasUsers()->hasAddress()->create();
        $this->user = User::first();

        $this->altUser = User::factory()->create();

        $this->file_name = 'The original document';
    }

    private function uploadAdditionalDocumentsMutation($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation uploadAdditionalDocuments ($property_id: ID!, $input: UploadAdditionalDocumentsInput!) {
                    uploadAdditionalDocuments(property_id: $property_id, input: $input) {
                      name
                      id
                    }
                  }
            ',
                [
                    'property_id' => $this->property->id,
                    'input' => $args,
                ],
            );
    }

    private function reuploadAdditionalDocumentsMutation($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation reuploadAdditionalDocuments ($property_id: ID!, $input: ReuploadAdditionalDocumentsInput!) {
                    reuploadAdditionalDocuments(property_id: $property_id, input: $input) {
                      name
                    }
                  }
            ',
                [
                    'property_id' => $this->property->id,
                    'input' => $args,
                ],
            );
    }

    /**
     * Test clients can upload documents
     *
     * @return void
     */
    public function test_clients_can_upload_documents()
    {
        Storage::fake();

        [$tempPath, $filePath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args);

        $testDocument = $this->property->getFirstMedia('documents');

        $this->assertEquals($testDocument->name, $this->file_name);
    }

    /**
     * Test unauthorised users cannot upload documents to property
     *
     * @return void
     */
    public function test_unauthorised_users_cannot_upload_documents()
    {
        Storage::fake();

        [$tempPath, $filePath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->altUser, $args)->assertSee('This action is unauthorized.');
    }

    /**
     * Test file extension validation
     *
     * @return void
     */
    public function test_file_extension_validation()
    {
        Storage::fake();

        [$tempPath, $filePath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'png',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args)->assertSee('Validation failed for the field [uploadAdditionalDocuments].');

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'jpg',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args)->assertSee('Validation failed for the field [uploadAdditionalDocuments].');

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'txt',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args)->assertSee('Validation failed for the field [uploadAdditionalDocuments].');
    }

    /**
     * Test clients can reupload documents
     *
     * @return void
     */
    public function test_clients_can_reupload_documents()
    {
        Storage::fake();

        $new_name = "I'm a new document!";

        [$tempPath, $filePath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args);

        Storage::assertMissing($tempPath);

        $original_document = $this->property->getFirstMedia('documents');

        $this->assertEquals($original_document->name, $this->file_name);

        [$newTempPath, $newFilePath] = $this->generateMockPdf();

        $inputData = [
            'file_id' => $original_document->id,
            'name' => $new_name,
            'uploaded_document' => [
                'key' => $newTempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->reuploadAdditionalDocumentsMutation($this->user, $inputData);

        // Refetch property
        $property = Property::find($this->property->id);

        $newDocument = $property->getFirstMedia('documents');

        $this->assertEquals($new_name, $newDocument->name);
    }

    /**
     * Test unauthorised clients cannot reupload documents
     *
     * @return void
     */
    public function test_unauthorised_clients_cannot_reupload_documents()
    {
        Storage::fake();

        $new_name = "I'm a new document!";

        [$tempPath, $filePath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => $this->file_name,
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->user, $args);

        Storage::assertMissing($tempPath);

        $original_document = $this->property->getFirstMedia('documents');

        $this->assertEquals($original_document->name, $this->file_name);

        [$newTempPath, $newFilePath] = $this->generateMockPdf();

        $inputData = [
            'file_id' => $original_document->id,
            'name' => $new_name,
            'uploaded_document' => [
                'key' => $newTempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->reuploadAdditionalDocumentsMutation($this->altUser, $inputData);

        // Refetch property
        $property = Property::find($this->property->id);

        $newDocument = $property->getFirstMedia('documents');

        $this->assertEquals($this->file_name, $newDocument->name);
    }
}
