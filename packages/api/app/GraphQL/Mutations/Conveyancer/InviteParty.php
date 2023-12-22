<?php

namespace App\GraphQL\Mutations\Conveyancer;

use App\Models\Property;
use App\Models\User;
use App\Notifications\InviteClient as NotificationsInviteClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

final class InviteParty
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        /** @var \App\Models\Property */
        $property = Property::query()
            ->where('conveyancer_id', $user->conveyancer->id)
            ->where('id', $args['input']['property_id'])
            ->first();

        $invitedUser = User::where('id', $args['input']['party_id'])->firstOrFail();

        $invitedUser->notify(new NotificationsInviteClient($invitedUser, $user, $property->address, $user->conveyancer));

        $invitedUser->update([
            'invite_code_sent_at' => Carbon::now(),
        ]);

        return true;
    }
}
