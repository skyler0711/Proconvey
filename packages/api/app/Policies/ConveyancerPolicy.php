<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConveyancerPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    public function view(User $user)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the billing email on their conveyancer.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function updateBillingEmail(?User $user)
    {
        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the existing party.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function updateExistingParty(?User $user)
    {
        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        if ($user && $user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the existing party.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function removeParty(?User $user)
    {
        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        if ($user && $user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the existing party.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function inviteParty(?User $user)
    {
        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        if ($user && $user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the existing party.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function removeGiftor(?User $user)
    {
        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        if ($user && $user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }
}
