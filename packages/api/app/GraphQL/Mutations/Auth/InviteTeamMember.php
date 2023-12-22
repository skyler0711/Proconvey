<?php

namespace App\GraphQL\Mutations\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\InviteTeamMember as NotificationsInviteTeamMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class InviteTeamMember
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        foreach ($args['team_members'] as $input) {
            $invitedUser = User::create([
                'conveyancer_id' => $user->conveyancer_id,
                'email' => $input['email'],
                'job_role' => $input['job_role'],
                'role' => UserRole::Conveyancer,
                'invite_code' => Str::random(32),
                'invite_code_sent_at' => Carbon::now(),
            ]);

            $invitedUser->notify(new NotificationsInviteTeamMember($invitedUser, $user));
        }

        return true;
    }
}
