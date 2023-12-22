<?php

namespace App\GraphQL\Mutations\Notifications;

use Illuminate\Support\Facades\Auth;

final class UpdateUserNotificationPreferences
{
    public function __invoke($_, array $args)
    {
        /** @var App\Models\User */
        $user = Auth::user();

        $user->notificationPreferences()->update([
            'getting_started_forms_completed' => $args['getting_started_forms_completed'],
            'onboarding_completed' => $args['onboarding_completed'],
            'client_new_document_uploads' => $args['client_new_document_uploads'],
        ]);

        return $user;
    }
}
