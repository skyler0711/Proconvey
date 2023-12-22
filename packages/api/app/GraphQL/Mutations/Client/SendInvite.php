<?php

namespace App\GraphQL\Mutations\Client;

use App\Models\Property;
use App\Models\User;
use App\Notifications\InviteClient as NotificationsInviteClient;
use Carbon\Carbon;

final class SendInvite
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $invitedUser = User::find($args['input']['user_id']);
        $currentUser = auth()->user();
        $property = Property::findOrFail($args['input']['property_id']);
        $address = $property->address;
        $conveyancer = $property->conveyancer;

        $invitedUser->notify(new NotificationsInviteClient($invitedUser, $currentUser, $address, $conveyancer));

        $invitedUser->update([
            'invite_code_sent_at' => Carbon::now(),
        ]);

        return $invitedUser;
    }
}
