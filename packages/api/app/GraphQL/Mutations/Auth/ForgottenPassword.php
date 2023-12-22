<?php

namespace App\GraphQL\Mutations\Auth;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class ForgottenPassword
{
    public function __invoke($_, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if (! $user) {
            return true;
        }

        $status = Password::sendResetLink([
            'email' => $args['email'],
        ], function (User $user, string $token) {
            Mail::to($user)->send(new ForgotPasswordMail($user, $token));
        });

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => ['Too many reset attempts. Please try again later.'],
            ]);
        }

        abort_if($status === Password::RESET_THROTTLED, 429);

        return true;
    }
}
