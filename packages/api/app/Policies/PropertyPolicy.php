<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Property $model)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->role === UserRole::Conveyancer) {
            return true;
        }

        if ($user->role === UserRole::Client && $model->users->pluck('id')->contains($user->id) && $model->archived_at === null) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Property $model)
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->role === UserRole::Conveyancer && $user->conveyancer_id === $model->conveyancer_id) {
            return true;
        }

        if ($user->role === UserRole::Client && $model->users->pluck('id')->contains($user->id) && $model->archived_at === null) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can archive a given property.
     *
     * @param  \App\Models\Property  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function archiveThisProperty(User $user, $args)
    {
        $property = Property::find($args['id']);

        if ($user->role === UserRole::Conveyancer && $property->conveyancer_id === $user->conveyancer_id && $property->archived_at === null) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can upload additional documents to the property.
     */
    public function uploadAdditionalDocuments(User $user, $args)
    {
        if ($user->properties()->where('properties.id', $args['property_id'])->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can upload proof of source of funds documents to the property.
     */
    public function uploadSofCheckDocuments(User $user, $args)
    {
        if ($user->properties()->where('properties.id', $args['property_id'])->exists()) {
            return true;
        }

        return false;
    }

     /* Determine whether the user can see the properties searched.
     *
     * @param  \App\Models\Property  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function searchProperties($args)
    {
        if ($args->role === UserRole::Conveyancer) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can invite another user.
     *
     * @param  \App\Models\Property  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function inviteClient(User $user, $args)
    {
        $invitee = User::find($args['input']['user_id']);
        $property = Property::find($args['input']['property_id']);

        $relatedUsersCount = $property->users()
            ->wherePivotIn('user_id', [$user->id, $invitee->id])
            ->count();
        if ($relatedUsersCount >= 2) {
            return true;
        }

        return false;
    }
}
