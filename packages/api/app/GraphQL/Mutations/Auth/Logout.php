<?php

namespace App\GraphQL\Mutations\Auth;

use Illuminate\Support\Facades\Auth;

final class Logout
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Auth::logout();

        return true;
    }
}
