<?php

namespace App\GraphQL\Mutations\Auth;

use App\Models\User;

final class DeleteOtherUser
{
    public function __invoke($_, array $args)
    {
        $user = User::where('id', $args['id']);

        $user->delete();

        return true;
    }
}
