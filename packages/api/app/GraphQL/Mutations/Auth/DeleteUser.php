<?php

namespace App\GraphQL\Mutations\Auth;

final class DeleteUser
{
    public function __invoke($_, array $args)
    {
        auth()->user()?->delete();

        return true;
    }
}
