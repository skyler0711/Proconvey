<?php

namespace Tests\Graphql\Property;

use App\Enums\UserRole;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ArchiveClientTest extends TestCase
{
    private $conveyancer;

    private $conveyancingFirm;

    private $altConveyancingFirm;

    private $altConveyancer;

    private $property;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancingFirm = Conveyancer::factory()->create([
            'id' => 2,
        ]);

        $this->altConveyancingFirm = Conveyancer::factory()->create([
            'id' => 3,
        ]);

        $this->conveyancer = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $this->conveyancingFirm->id,
        ]);

        $this->property = Property::factory()->has(User::factory()->state([
            'role' => UserRole::Client,
        ]), 'users')->create([
            'id' => 1,
            'conveyancer_id' => $this->conveyancingFirm->id,
            'archived_at' => null,
        ]);
    }

// * Team member can archive property
// * Team member cannot archive existing archived property
// * Unauthorised users cannot archive a property
// * Conveyancer users from other conveyancer companies cannot archive properties that don’t belong to them

    public function test_authorised_conveyancer_can_archive_property()
    {
        $this->assertEquals(null, $this->property->archived_at);

        $response = $this
            ->actingAs($this->conveyancer)
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation ($id: ID!) {
                    archiveProperty(id: $id) {
                      id
                      archived_at
                    }
                  } 
            ', [
                    'id' => $this->property->id,
                ]
            )
            ->assertGraphQLErrorFree()
            ->assertJson([
                'data' => [
                    'archiveProperty' => [
                        'id' => (string) $this->property->id,
                    ],
                ],
            ]);

        $property = Property::find($this->property->id);

        $this->assertNotEquals(null, $property->archived_at);
    }

    public function test_users_cannot_archive_previously_archived_property()
    {
        $time = Carbon::now()->subDay();

        $this->property->archived_at = $time;
        $this->property->save();

        $response = $this
        ->actingAs($this->conveyancer)
        ->graphQL(
            /** @lang GraphQL */
            '
                mutation ($id: ID!) {
                    archiveProperty(id: $id) {
                      id
                      archived_at
                    }
                  }
            ', [
                'id' => $this->property->id,
            ]
        )
        ->assertJsonFragment([
            'message' => 'This action is unauthorized.',
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $this->property->id,
            'archived_at' => $time,
        ]);
    }

    public function test_unauthorised_conveyancers_cannot_archive_property()
    {
        $this->altConveyancer = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $this->altConveyancingFirm->id,
        ]);

        $response = $this
        ->actingAs($this->altConveyancer)
        ->graphQL(
            /** @lang GraphQL */
            '
                mutation ($id: ID!) {
                    archiveProperty(id: $id) {
                      id
                      archived_at
                    }
                  }
            ', [
                'id' => $this->property->id,
            ]
        )
        ->assertJsonFragment([
            'message' => 'This action is unauthorized.',
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $this->property->id,
            'archived_at' => null,
        ]);
    }

    public function test_clients_cannot_archive_property()
    {
        $this->client = User::factory()->create([
            'role' => UserRole::Client,
        ]);

        $response = $this
        ->actingAs($this->client)
        ->graphQL(
            /** @lang GraphQL */
            '
                mutation ($id: ID!) {
                    archiveProperty(id: $id) {
                      id
                      archived_at
                    }
                  }
            ', [
                'id' => $this->property->id,
            ]
        )
        ->assertJsonFragment([
            'message' => 'This action is unauthorized.',
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $this->property->id,
            'archived_at' => null,
        ]);
    }
}
