<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Perform pre-authorization checks.
     */
    public function view(User $user, Form $form): bool|null
    {
        return true;
    }

    public function saveProvidedAnswers(?User $user)
    {
        if ($user && $user->role === UserRole::Client) {
            return true;
        }

        if ($user && $user->role === UserRole::Conveyancer) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        if ($user && $user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }
}
