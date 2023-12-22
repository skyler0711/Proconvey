<?php

namespace App\GraphQL\Mutations\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class Register
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = User::create([
            'email' => Arr::get($args, 'email'),
            'password' => Arr::get($args, 'password'),
            'role' => UserRole::Conveyancer,
        ]);

        Auth::login($user);

        return $user;
    }
}
