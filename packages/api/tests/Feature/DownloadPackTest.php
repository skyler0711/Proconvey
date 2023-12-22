<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadPackTest extends TestCase
{
    private $property;

    private $user;

    private $altUser;

    private $client;

    private $conveyancingFirm;

    private $altConveyancingFirm;

    private $caseReference;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancingFirm = Conveyancer::factory()->create();

        $this->altConveyancingFirm = Conveyancer::factory()->create();

        $this->property = Property::factory()
        ->hasUsers()
        ->hasAddress()
        ->create([
            'conveyancer_id' => $this->conveyancingFirm->id,
        ]);

        $this->user = User::factory()->create([
            'conveyancer_id' => $this->conveyancingFirm->id,
            'role' => UserRole::Conveyancer,
        ]);

        $this->altUser = User::factory()->create([
            'conveyancer_id' => $this->altConveyancingFirm->id,
            'role' => UserRole::Conveyancer,
        ]);

        $this->client = User::first();

        $this->caseReference = $this->property->case_reference;
    }

    private function getPack($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance

        ->graphql(
            /** @lang GraphQL */
            '        
            query downloadPack($id: ID!) {
                property(id: $id) {
                    all_documents_link
                }
            }',
            [
                'id' => $this->property->id,
            ],
        );
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

    /**
     * Test conveyancer can download pack documents
     *
     * @return void
     */
    public function test_conveyancers_can_download_pack()
    {
        Storage::fake();

        [$tempPath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => 'test document',
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->client, $args);

        $this->getPack($this->user, $this->property->id)->assertJson([
            'data' => [
                'property' => [
                    'all_documents_link' => "http://localhost:9000/local/tmp/$this->caseReference-pack.zip",
                ],
            ],
        ]);
    }

    /**
     * Test conveyancer can download pack documents
     *
     * @return void
     */
    public function test_unauthorised_conveyancers_cannot_download_pack()
    {
        Storage::fake();

        [$tempPath] = $this->generateMockPdf();

        Storage::assertExists($tempPath);

        $args = [
            'name' => 'test document',
            'uploaded_document' => [
                'key' => $tempPath,
                'extension' => 'pdf',
            ],
        ];

        $this->uploadAdditionalDocumentsMutation($this->client, $args);

        $this->getPack($this->altUser, $this->property->id)->assertJsonFragment([
            'message' => 'This action is unauthorized.',
        ]);
    }
}
