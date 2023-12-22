<?php

namespace Tests\Graphql\Auth;

use Tests\TestCase;

class ClientRegisterTest extends TestCase
{
    public function testRequiredValidation()
    {
        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation RegisterClient($input: RegisterClientInput!) {
                    registerClient(input: $input) {
                        email
                    }
                }
            ', [
                'input' => [
                    'email' => '',
                    'password' => '',
                    'user_id' => '1',
                    'invite_code' => '123',
                ],
            ])
            ->assertGraphQLValidationKeys([
                'input.email',
                'input.password',
            ]);
    }
}
