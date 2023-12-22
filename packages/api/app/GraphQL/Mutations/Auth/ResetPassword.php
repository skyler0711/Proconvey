<?php

namespace App\GraphQL\Mutations\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ResetPassword
{
    public function __invoke($_, array $args)
    {
        /**
         * @param  null  $_
         * @param  array{}  $args
         */
        $passwordReset = DB::table('password_resets')
            ->where('email', $args['email'])
            ->where('created_at', '>', now()->subSeconds(config('auth.password_timeout'))); // default 1 hour

        if ($passwordReset->doesntExist() || ! Hash::check($args['token'], $passwordReset->first()?->token)) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided token is invalid. Please request for a new one.'],
                ]
            );
        }

        Password::reset(
            [
                'email' => $args['email'],
                'password' => $args['password'],
                'token' => $args['token'],
            ],
            function ($user, $password) {
                $user->forceFill(
                    [
                        'password' => $password,
                        'remember_token' => Str::random(60),
                    ]
                );
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return true;
    }
}
