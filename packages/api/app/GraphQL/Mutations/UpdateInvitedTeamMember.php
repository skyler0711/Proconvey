<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

final class UpdateInvitedTeamMember
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $user->update([
            'job_bio' => $args['job_bio'],
        ]);

        if (isset($args['profile_image'])) {
            if ($user->profile_image) {
                $user->profile_image->delete();
            }

            $user
              ->addMediaFromDisk($args['profile_image']['key'])
              ->usingFileName(explode('/', $args['profile_image']['key'])[1].'.'.$args['profile_image']['extension'])
              ->toMediaCollection('profile_image');
        }

        return $user;
    }
}
