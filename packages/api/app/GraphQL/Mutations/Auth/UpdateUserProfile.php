<?php

namespace App\GraphQL\Mutations\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class UpdateUserProfile
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $newData = [
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'email' => $args['email'],
            'phone' => $args['phone'],
        ];

        if (isset($args['title'])) {
            $newData['title'] = $args['title'];
        }

        if (isset($args['suffix'])) {
            $newData['suffix'] = $args['suffix'];
        }

        if (isset($args['job_bio'])) {
            $newData['job_bio'] = $args['job_bio'];
        }

        if (isset($args['job_role'])) {
            $newData['job_role'] = $args['job_role'];
        }

        if ($user->invite_code) {
            $newData['invite_code'] = null;
            $newData['invite_code_sent_at'] = null;
        }

        if (Hash::check($args['password'], $user->password)) {
            $newData['password'] = $args['newPassword'];
        }

        $user->update($newData);

        if (isset($args['profile_image'])) {
            if ($user->profile_image) {
                $user->profile_image->delete();
            }

            $user
                ->addMediaFromDisk($args['profile_image']['key'])
                ->usingFileName(explode('/', $args['profile_image']['key'])[1].'.'.$args['profile_image']['extension'])
                ->toMediaCollection('profile_image');
        } else {
            if ($user->profile_image && $args['profile_image'] === null) {
                $user->profile_image->delete();
            }
        }

        $user->save();

        return $user;
    }
}
