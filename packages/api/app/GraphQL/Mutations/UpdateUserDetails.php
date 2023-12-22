<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

final class UpdateUserDetails
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
            'job_role' => $args['job_role'],
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'title' => $args['title'],
            'suffix' => $args['suffix'],
            'phone' => $args['phone'],
            'sra_clc_number' => $args['sra_clc_number'],
        ]);

        if (isset($args['profile_image'])) {
            if ($user->profile_image) {
                $user->profile_image->delete();
            }

            $user
              ->addMediaFromDisk($args['profile_image']['key'])
              ->usingFileName(explode('/', $args['profile_image']['key'])[1].'.'.$args['profile_image']['extension'])
              ->toMediaCollection('profile_image');
        } else {
            if ($user->profile_image && array_key_exists('profile_image', $args)) {
                $user->profile_image->delete();
            }
        }

        return $user;
    }
}
