<?php

namespace Tests\Graphql\Auth;

use App\Enums\UserRole;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgottenPasswordTest extends TestCase
{
    public function testHappyPath(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => UserRole::Client,
            'email' => 'client@coreblue.com',
        ]);

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation forgottenPassword($email: String!) {
                    forgottenPassword(email: $email)
                }
            ', [
                'email' => $user->email,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonFragment([
                'forgottenPassword' => true,
            ]);

        Mail::assertSent(ForgotPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function testSilentFailureOnEmailNotFound()
    {
        Notification::fake();

        $email = fake()->safeEmail();

        $this
            ->graphQL(/** @lang GraphQL */ '
                mutation forgottenPassword($email: String!) {
                    forgottenPassword(email: $email)
                }
            ', [
                'email' => $email,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonFragment([
                'forgottenPassword' => true,
            ]);

        Notification::assertNothingSent();
    }
}
