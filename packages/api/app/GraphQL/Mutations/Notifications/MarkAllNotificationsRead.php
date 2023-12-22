<?php

namespace App\GraphQL\Mutations\Notifications;

use Illuminate\Support\Facades\Auth;

final class MarkAllNotificationsRead
{
    public function __invoke($_, array $args)
    {
        $user = Auth::user();

        $user->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        return $user;
    }
}
