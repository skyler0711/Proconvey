<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Tests\TestCase;

class InviteOtherUserTest extends TestCase
{
    private $conveyancer;

    private $user;

    private $invitee;

    private $unauthorisedUser;

    private $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conveyancer = Conveyancer::factory()->create();

        $this->user = User::factory()->create([
            'role' => UserRole::Client,
        ]);

        $this->invitee = User::factory()->create([
            'role' => UserRole::Client,
            'invite_code_sent_at' => null,
        ]);

        $this->unauthorisedUser = User::factory()->create([
            'role' => UserRole::Client,
        ]);

        $this->property = Property::factory()->has(Address::factory())->create([
            'conveyancer_id' => $this->conveyancer->id,
        ]);

        $this->user->properties()->attach($this->property);
    }

    private function inviteUserMutation($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation sendInvite($input: SendInviteInput!) {
                    sendInvite(input: $input) {
                        id
                        invite_code_sent_at
                    }
                  }
            ',
                [
                    'input' => $args,
                ],
            );
    }

    public function test_user_can_invite_other_users()
    {
        $this->invitee->properties()->attach($this->property);

        $args = [
            'user_id' => $this->invitee->id,
            'property_id' => $this->property->id,
        ];

        $this->assertNull($this->invitee->invite_code_sent_at);

        $this->inviteUserMutation($this->user, $args)->assertJsonFragment([
            'id' => (string) $this->invitee->id,
        ]);
    }

    public function test_user_cannot_invite_unauthorised_users()
    {
        $args = [
            'user_id' => $this->invitee->id,
            'property_id' => $this->property->id,
        ];

        $this->inviteUserMutation($this->user, $args)->assertSee('This action is unauthorized');
    }

    public function test_unauthorised_user_cannot_invite_anyone()
    {
        $args = [
            'user_id' => $this->invitee->id,
            'property_id' => $this->property->id,
        ];

        $this->inviteUserMutation($this->unauthorisedUser, $args)->assertSee('This action is unauthorized');

        $this->invitee->properties()->attach($this->property);

        $this->inviteUserMutation($this->unauthorisedUser, $args)->assertSee('This action is unauthorized');
    }
}
