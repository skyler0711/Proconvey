<?php

namespace Tests\Graphql\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testHappyPath()
    {
        $user = User::factory()->create([
            'role' => UserRole::Conveyancer,
        ]);

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Login($input: LoginInput!) {
                    login(input: $input) {
                        first_name
                        last_name
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => $user->email,
                    'password' => 'password',
                ],
            ])
            ->assertGraphQLErrorFree()
            ->assertGraphQLValidationPasses()
            ->assertJsonFragment([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]);
    }

    public function testAdminsCannotLogin()
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Login($input: LoginInput!) {
                    login(input: $input) {
                        first_name
                        last_name
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => $user->email,
                    'password' => 'password',
                ],
            ])
            ->assertGraphQLErrorMessage('These credentials do not match our records.');
    }

    public function testUnknownUserCannotLogin()
    {
        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Login($input: LoginInput!) {
                    login(input: $input) {
                        first_name
                        last_name
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => fake()->safeEmail(),
                    'password' => 'password',
                ],
            ])
            ->assertGraphQLErrorMessage('These credentials do not match our records.');
    }

    public function testValidation()
    {
        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation Login($input: LoginInput!) {
                    login(input: $input) {
                        first_name
                        last_name
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => '',
                    'password' => '',
                ],
            ])
            ->assertGraphQLValidationKeys(['input.email', 'input.password']);
    }
}
