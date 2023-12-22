<?php

namespace App\GraphQL\Mutations\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class Login
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = User::query()
            ->where('email', $args['email'])
            ->whereNot('role', UserRole::Admin)
            ->first();

        if (! $user || ! Hash::check($args['password'], $user->password)) {
            return ValidationException::withMessages([
                'input.password' => 'These credentials do not match our records.',
            ]);
        }

        Auth::login($user);

        return $user;
    }
}
