<?php

namespace App\GraphQL\Mutations\Auth;

use App\Models\User;
use App\Notifications\InviteTeamMember as NotificationsInviteTeamMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

final class ResendInvite
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $invitedUser = User::where('email', $args['email'])->first();

        $invitedUser->notify(new NotificationsInviteTeamMember($invitedUser, $user));

        $invitedUser->update([
            'invite_code_sent_at' => Carbon::now(),
        ]);

        return true;
    }
}
