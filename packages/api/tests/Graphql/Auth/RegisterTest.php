<?php

namespace Tests\Graphql\Auth;

use App\Models\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testHappyPath()
    {
        $user = User::factory()->make();

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Register($input: RegisterInput!) {
                    register(input: $input) {
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => $user->email,
                    'password' => '4V3ryC0mpl!c4t3dP455W0rd',
                ],
            ])
            ->assertGraphQLErrorFree()
            ->assertGraphQLValidationPasses()
            ->assertJsonFragment([
                'email' => $user->email,
            ]);
    }

    public function testRequiredValidation()
    {
        $user = User::factory()->make();

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Register($input: RegisterInput!) {
                    register(input: $input) {
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => '',
                    'password' => '',
                ],
            ])
            ->assertGraphQLValidationKeys([
                'input.email',
                'input.password',
            ]);
    }

    public function testCannotRegisterWithExistingEmail()
    {
        $user = User::factory()->create();

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Register($input: RegisterInput!) {
                    register(input: $input) {
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => $user->email,
                    'password' => '4V3ryC0mpl!c4t3dP455W0rd',
                ],
            ])
            ->assertGraphQLValidationKeys(['input.email']);
    }
}
