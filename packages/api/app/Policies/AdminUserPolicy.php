<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminUserPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    public function viewAny(User $user)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    public function view(User $user, AdminUser $model)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    public function update(User $user, AdminUser $model)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }

    public function delete(User $user, AdminUser $model)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return false;
    }
}
