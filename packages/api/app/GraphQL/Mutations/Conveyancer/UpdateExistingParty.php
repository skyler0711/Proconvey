<?php

namespace App\GraphQL\Mutations\Conveyancer;

use App\Enums\UserRole;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class UpdateExistingParty
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Conveyancer */
        $conveyancer = Auth::user()->conveyancer;

        $user = User::find(Arr::get($args, 'user_id'));

        $property = Property::where('conveyancer_id', $conveyancer->id)
            ->find(Arr::get($args, 'property_id'));

        $user->update([
            'title' => Arr::get($args, 'title'),
            'first_name' => Arr::get($args, 'first_name'),
            'middle_name' => Arr::get($args, 'middle_name'),
            'last_name' => Arr::get($args, 'last_name'),
            'email' => Arr::get($args, 'email'),
            'role' => UserRole::Client,
        ]);

        $user->address()->updateOrCreate([
            'line_1' => Arr::get($args, 'address.line_1'),
            'line_2' => Arr::get($args, 'address.line_2'),
            'city' => Arr::get($args, 'address.city'),
            'postcode' => Arr::get($args, 'address.postcode'),
        ], [
            'line_1' => Arr::get($args, 'address.line_1'),
            'line_2' => Arr::get($args, 'address.line_2'),
            'city' => Arr::get($args, 'address.city'),
            'postcode' => Arr::get($args, 'address.postcode'),
        ]);

        $property->users()->updateExistingPivot($user->id, [
            'representation' => Arr::get($args, 'representation'),
        ]);

        return true;
    }
}
