<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvidedAnswerPolicy
{
    use HandlesAuthorization;

    /**
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteMortgage(User $user, $args)
    {
        $property = Property::find($args['property_id']);

        if (! $property) {
            return false;
        }

        $teamMembers = $property->conveyancer->teamMembers->collect()->pluck('id');

        if ($teamMembers->contains($user->id)) {
            return true;
        }

        return false;
    }
}
