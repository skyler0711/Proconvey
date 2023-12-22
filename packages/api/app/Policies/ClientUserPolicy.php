<?php

namespace App\Policies;

use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientUserPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return true;
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, ClientUser $model)
    {
        return true;
    }

    public function update(User $user, ClientUser $model)
    {
        return true;
    }

    public function delete(User $user, ClientUser $model)
    {
        return true;
    }
}
