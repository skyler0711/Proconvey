<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Step;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StepPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Step $step)
    {
        switch ($user->role) {
            case UserRole::Admin:
            case UserRole::Conveyancer:
            case UserRole::Client:
                return true;

            default:
                return false;
        }
    }
}
