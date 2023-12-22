<?php

namespace App\GraphQL\Mutations\Conveyancer;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class RemoveParty
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $conveyancer = Auth::user()->conveyancer;

        $party = User::where('id', Arr::get($args['input'], 'party_id'))->firstOrFail();

        $property = $party
        ->properties()
        ->where('conveyancer_id', $conveyancer->id)
        ->find(Arr::get($args['input'], 'property_id'));

        $property->users()->detach($party->id);

        return true;
    }
}
