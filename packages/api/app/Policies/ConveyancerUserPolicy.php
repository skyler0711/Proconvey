<?php

namespace App\Policies;

use App\Models\ConveyancerUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConveyancerUserPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return false;
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, ConveyancerUser $model)
    {
        return true;
    }

    public function update(User $user, ConveyancerUser $model)
    {
        return true;
    }

    public function delete(User $user, ConveyancerUser $model)
    {
        return true;
    }
}
