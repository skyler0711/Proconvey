<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

final class UpdateClientDetails
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
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'phone' => $args['phone'],
            'title' => $args['title'],
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
            if ($user->profile_image && is_null($args['profile_image'])) {
                $user->profile_image->delete();
            }
        }

        return $user;
    }
}
