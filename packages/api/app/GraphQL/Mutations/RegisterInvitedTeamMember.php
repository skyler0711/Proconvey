<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class RegisterInvitedTeamMember
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = User::where('id', $args['user_id'])->first();

        if (Hash::check($user->invite_code, $args['invite_code'])) {
            if ($args['email'] === $user->email) {
                $user->update([
                    'first_name' => $args['first_name'],
                    'last_name' => $args['last_name'],
                    'title' => $args['title'],
                    'suffix' => $args['suffix'],
                    'password' => $args['password'],
                    'email_verified_at' => now(),

                ]);
            } else {
                throw ValidationException::withMessages([
                    'email' => ['The email does not match the invite.'],
                ]);
            }
            Auth::login($user);
        } else {
            throw ValidationException::withMessages([
                'email' => ['The invite code does not match the invite.'],
            ]);
        }

        return $user;
    }
}
