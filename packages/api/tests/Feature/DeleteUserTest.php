<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Conveyancer;
use App\Models\User;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    private $conveyancer;

    private $alt_conveyancer;

    private $user;

    private $alt_user;

    private $unauthorised_user;

    private $user_from_other_conveyancer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancer = Conveyancer::factory()->create();
        $this->alt_conveyancer = Conveyancer::factory()->create();

        $this->user = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $this->conveyancer->id,
        ]);

        $this->alt_user = User::factory()->create([
            'role' => UserRole::Client,
            'conveyancer_id' => $this->conveyancer->id,
        ]);
    }

    private function deleteOtherUserMutation($user, $id)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation deleteOtherUser($id: ID!) {
                    deleteOtherUser(id: $id)
                  }
            ',
                [
                    'id' => $id,
                ],
            );
    }

    public function test_user_can_delete_other_user()
    {
        $this->assertDatabaseCount('users', 2);

        $this->deleteOtherUserMutation($this->user, $this->alt_user->id)->assertJsonFragment([
            'deleteOtherUser' => true,
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_users_cannot_delete_users_from_other_conveyancers()
    {
        $this->user_from_other_conveyancer = User::factory()->create([
            'role' => UserRole::Client,
            'conveyancer_id' => $this->alt_conveyancer->id,
        ]);

        $this->assertDatabaseCount('users', 3);

        $this->deleteOtherUserMutation($this->user, $this->user_from_other_conveyancer->id)->assertSee('This action is unauthorized.');

        $this->assertDatabaseCount('users', 3);
    }

    public function test_unauthorised_user_cannot_delete_other_user()
    {
        $this->unauthorised_user = User::factory()->create([
            'role' => UserRole::Client,
            'conveyancer_id' => $this->conveyancer->id,
        ]);

        $this->assertDatabaseCount('users', 3);

        $this->deleteOtherUserMutation($this->unauthorised_user, $this->alt_user->id)->assertSee('This action is unauthorized.');

        $this->assertDatabaseCount('users', 3);
    }
}
