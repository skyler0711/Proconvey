<?php

namespace Tests\Graphql\Media;

use App\Enums\DocumentType;
use App\Enums\UserRole;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    private $conveyancingFirm;

    private $conveyancer;

    private $property;

    private $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancingFirm = Conveyancer::factory()->create();

        $this->conveyancer = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $this->conveyancingFirm->id,
        ]);

        $this->conveyancingFirm->teamMembers()->save($this->conveyancer);

        $this->property = Property::factory()->create([
            'id' => 1,
            'conveyancer_id' => $this->conveyancingFirm->id,
            'archived_at' => null,
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->create($fileName = 'test-media.pdf', $kilobytes = 1000);

        $this->property
            ->addMedia($file)
            ->usingFileName('test-media.pdf')
            ->withCustomProperties([
                'type' => DocumentType::Additional,
            ])
            ->toMediaCollection('documents');

        $this->assertEquals($this->property->getMedia('documents')->count(), 1);

        $this->media = $this->property->getMedia('documents')->first();
    }

    public function test_authorized_client_can_view_media()
    {
        $client = User::factory()->create([
            'role' => UserRole::Client,
        ]);

        $this->property->users()->attach($client);

        $this
            ->actingAs($client)
            ->graphQL(
                /** @lang GraphQL */
                '
                query media($id: ID!) {
                    media(id: $id) {
                        id
                        url
                        name
                        custom_properties
                    }
                }
            ',
                [
                    'id' => $this->media->id,
                ]
            )
            ->assertGraphQLErrorFree()
            ->assertJson([
                'data' => [
                    'media' => [
                        'id' => $this->media->id,
                        'url' => $this->media->getUrl(),
                        'name' => 'test-media',
                        'custom_properties' => [
                            'type' => DocumentType::Additional,
                        ],
                    ],
                ],
            ]);
    }

    public function test_unauthorized_client_cannot_view_media()
    {
        $client = User::factory()->create([
            'role' => UserRole::Client,
        ]);

        $this
            ->actingAs($client)
            ->graphQL(
                /** @lang GraphQL */
                '
                query media($id: ID!) {
                    media(id: $id) {
                        id
                        url
                        name
                        custom_properties
                    }
                }
            ',
                [
                    'id' => $this->media->id,
                ]
            )
            ->assertGraphQLErrorMessage('This action is unauthorized.')
            ->assertJsonMissing([
                'data' => [
                    'media' => [
                        'id' => 1,
                        'url' => $this->media->getUrl(),
                        'name' => 'test-media',
                        'custom_properties' => [
                            'type' => DocumentType::Additional,
                        ],
                    ],
                ],
            ]);
    }

    public function test_authorised_conveyancer_can_view_media()
    {
        $this
            ->actingAs($this->conveyancer)
            ->graphQL(
                /** @lang GraphQL */
                '
                query media($id: ID!) {
                    media(id: $id) {
                        id
                        url
                        name
                        custom_properties
                    }
                }
            ',
                [
                    'id' => $this->media->id,
                ]
            )
            ->assertGraphQLErrorFree()
            ->assertJson([
                'data' => [
                    'media' => [
                        'id' => $this->media->id,
                        'url' => $this->media->getUrl(),
                        'name' => 'test-media',
                        'custom_properties' => [
                            'type' => DocumentType::Additional,
                        ],
                    ],
                ],
            ]);
    }

    public function test_unauthorized_conveyancer_cannot_view_media()
    {
        $newFirm = Conveyancer::factory()->create();

        $newConveyancer = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $newFirm->id,
        ]);

        $this
            ->actingAs($newConveyancer)
            ->graphQL(
                /** @lang GraphQL */
                '
                query media($id: ID!) {
                    media(id: $id) {
                        id
                        url
                        name
                        custom_properties
                    }
                }
            ',
                [
                    'id' => $this->media->id,
                ]
            )
            ->assertGraphQLErrorMessage('This action is unauthorized.')
            ->assertJsonMissing([
                'data' => [
                    'media' => [
                        'id' => 1,
                        'url' => $this->media->getUrl(),
                        'name' => 'test-media',
                        'custom_properties' => [
                            'type' => DocumentType::Additional,
                        ],
                    ],
                ],
            ]);
    }
}
