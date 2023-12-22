<?php

namespace App\GraphQL\Mutations\Conveyancer;

use App\Enums\PropertyUserRole;
use App\Enums\UserRole;
use App\Models\Property;
use App\Notifications\InviteClient as NotificationsInviteClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class AddNewParty
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        /** @var \App\Models\Conveyancer */
        $conveyancer = auth()->user()->conveyancer;

        /** @var \App\Models\Property */
        $property = Property::query()
            ->where('conveyancer_id', $conveyancer->id)
            ->where('id', Arr::get($args, 'client_id'))
            ->first();

        if (Arr::get($args, 'party_type') === PropertyUserRole::Owner && Arr::get($args, 'owner_type') === 'company') {
            $newUser = $property->users()->create([
                'first_name' => Arr::get($args, 'first_name'),
                'last_name' => Arr::get($args, 'last_name'),
                'email' => Arr::get($args, 'email'),
                'role' => UserRole::Client,
            ], [
                'role' => Arr::get($args, 'party_type') === PropertyUserRole::Owner ? PropertyUserRole::Owner : Arr::get($args, 'party_type'),
                'representation' => implode(' ', [Arr::get($args, 'first_name'), Arr::get($args, 'last_name')]),
            ]);

            if ($args['id_check']) {
                $idVerification = $newUser->idVerification()->create([
                    'user_id' => $newUser->id,
                    'conveyancer_id' => $conveyancer->id,
                ]);

                $property->users()->updateExistingPivot($newUser->id, [
                    'id_verification_id' => $idVerification->id,
                ]);
            }
        }

        if (Arr::get($args, 'owner_type') === 'individual' && Arr::get($args, 'representation') === 'acting_for_themselves') {
            $newUser = $property->users()->create([
                'first_name' => Arr::get($args, 'owner_first_name'),
                'last_name' => Arr::get($args, 'owner_last_name'),
                'email' => Arr::get($args, 'owner_email'),
                'role' => UserRole::Client,
            ], [
                'role' => Arr::get($args, 'party_type') === PropertyUserRole::Owner ? PropertyUserRole::Owner : Arr::get($args, 'party_type'),
                'representation' => implode(' ', [Arr::get($args, 'owner_first_name'), Arr::get($args, 'owner_last_name')]),
            ]);

            if ($args['id_check']) {
                $idVerification = $newUser->idVerification()->create([
                    'user_id' => $newUser->id,
                    'conveyancer_id' => $conveyancer->id,
                ]);

                $property->users()->updateExistingPivot($newUser->id, [
                    'id_verification_id' => $idVerification->id,
                ]);
            }
        }

        if (Arr::get($args, 'representation') === 'attorney' || Arr::get($args, 'party_type') === 'attorney' ||
        Arr::get($args, 'representation') === 'deputy' || Arr::get($args, 'party_type') === 'deputy' ||
        Arr::get($args, 'representation') === 'executor' || Arr::get($args, 'party_type') === 'executor') {
            $newUser = $property->users()->create([
                'first_name' => Arr::get($args, 'representative_first_name'),
                'last_name' => Arr::get($args, 'representative_last_name'),
                'email' => Arr::get($args, 'representative_email'),
                'role' => UserRole::Client,
            ], [
                'role' => Arr::get($args, 'party_type') === PropertyUserRole::Owner ? PropertyUserRole::Owner : Arr::get($args, 'party_type'),
                'representation' => implode(' ', [Arr::get($args, 'representative_first_name'), Arr::get($args, 'representative_last_name')]),
            ]);

            if ($args['id_check']) {
                $idVerification = $newUser->idVerification()->create([
                    'user_id' => $newUser->id,
                    'conveyancer_id' => $conveyancer->id,
                ]);

                $property->users()->updateExistingPivot($newUser->id, [
                    'id_verification_id' => $idVerification->id,
                ]);
            }
        }

        $newUser->notify(new NotificationsInviteClient($newUser, $user, $property->address, $conveyancer));

        return true;
    }
}
