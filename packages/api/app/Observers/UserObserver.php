<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    public function created(User $user)
    {
        $user->notificationPreferences()->create();
    }

    public function saving(User $user)
    {
        if ($user->isDirty('password') && Hash::info($user->password)['algo'] === null) {
            $user->password = Hash::make($user->password);
        }

        if ($user->isDirty('invite_code')) {
            $user->invite_code = Hash::make($user->invite_code);
        }
    }

    public function beforeDeleting(User $user)
    {
        if ($user->conveyancer->users()->count() === 1) {
            $user->conveyancer->delete();
            $user->conveyancer->teamMembers->each->delete();
        }

        if ($user->address) {
            $user->address->delete();
        }

        if ($user->profile_image) {
            $user->profile_image->delete();
        }
    }
}
