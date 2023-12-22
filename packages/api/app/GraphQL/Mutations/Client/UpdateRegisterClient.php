<?php

namespace App\GraphQL\Mutations\Client;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class UpdateRegisterClient
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = User::find(Arr::get($args, 'user_id'));

        // Client has already verified their account
        if (! is_null($user?->email_verified_at)) {
            return ValidationException::withMessages([
                'input.email' => 'Email has already been verified.',
            ]);
        }

        // Client invite link does not match the user record invite_code
        if (! $user || ! Hash::check($user?->invite_code, $args['invite_code'])) {
            throw ValidationException::withMessages([
                'input.email' => ['The invite code does not match the invite.'],
            ]);
        }

        // TODO: Invite link expiration

        // Update verifying clients details
        $user->update([
            'password' => $args['password'],
            'email_verified_at' => Carbon::now(),
            'invite_code' => null,
        ]);

        Auth::login($user);

        return $user;
    }
}
