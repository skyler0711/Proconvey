<?php

namespace App\GraphQL\Mutations\Client;

use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Enums\PropertyUserRole;
use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\IdVerification;
use App\Models\Property;
use App\Models\User;
use App\Notifications\InviteClient as NotificationsInviteClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class InviteNewClient
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        /** @var \App\Models\Property */
        $property = Property::create([
            'conveyancer_id' => $user->conveyancer_id,
            'type' => $args['type'],
            'case_reference' => Arr::get($args, 'case_reference'),
            'letters_required' => Arr::get($args, 'letters_required'),
            'id_check_required' => Arr::get($args, 'id_check_required'),
            'sale_price' => Arr::get($args, 'sale_price'),
            'type' => Arr::get($args, 'type'),
            'conveyancing_fee' => Arr::get($args, 'conveyancing_fee'),
            'fee_earner_id' => Arr::get($args, 'fee_earner_id'),
            'payment_required' => Arr::get($args, 'payment_required'),
            'payment_amount' => Arr::get($args, 'payment_amount'),
            'sof_check_required' => Arr::get($args, 'sof_check_required'),
        ]);

        $property->address()->create([
            'line_1' => $args['address']['line_1'],
            'line_2' => $args['address']['line_2'],
            'city' => $args['address']['city'],
            'postcode' => $args['address']['postcode'],
            'uprn' => $args['address']['uprn'] ?? '',
        ]);

        // Check if this user already exists
        $invitedUser = User::query()
            ->where('email', $args['email'])
            ->first();

        // Create a new user if they don't exist
        if (! $invitedUser) {
            /** @var \App\Models\User */
            $invitedUser = $property->users()->create([
                'first_name' => $args['first_name'],
                'last_name' => $args['last_name'],
                'role' => UserRole::Client,
                'email' => $args['email'],
                'invite_code' => Str::random(32),
            ], [
                'role' => match ($args['type']) {
                    PropertyType::Sale => PropertyUserRole::Owner,
                    PropertyType::Purchase => PropertyUserRole::Buyer,
                    PropertyType::Remortgage => PropertyUserRole::Remortgager,
                },
                'is_primary_user' => true,
            ]);

            $idVerificationCreate = $invitedUser->idVerification()->create([
                'conveyancer_id' => $user->conveyancer_id,
                'user_id' => $invitedUser->id,
            ]);

            $address = $invitedUser->address()->create([
                'line_1' => $args['address']['line_1'],
                'line_2' => $args['address']['line_2'],
                'city' => $args['address']['city'],
                'postcode' => $args['address']['postcode'],
                'uprn' => $args['address']['uprn'] ?? '',
            ]);

            $property->users()->updateExistingPivot($invitedUser->id, [
                'id_verification_id' => $idVerificationCreate->id,
            ]);

            $invitedUser->notify(new NotificationsInviteClient($invitedUser, $user, $address, $user->conveyancer));
        } else {
            // Query the ID Verification
            $idVerification = IdVerification::query()
                ->where('user_id', $invitedUser->id)
                ->where('conveyancer_id', $user->conveyancer_id)
                ->first();

            if (! $idVerification) {
                $idVerification = IdVerification::query()
                    ->create([
                        'conveyancer_id' => $user->conveyancer_id,
                        'user_id' => $invitedUser->id,
                    ]);
            }

            // Add the user to the property
            $property->users()->attach($invitedUser, [
                'role' => match ($args['type']) {
                    PropertyType::Sale => PropertyUserRole::Owner,
                    PropertyType::Purchase => PropertyUserRole::Buyer,
                    PropertyType::Remortgage => PropertyUserRole::Remortgager,
                },
                'is_primary_user' => true,
            ]);

            $property->users()->updateExistingPivot($invitedUser->id, [
                'id_verification_id' => $idVerification->id,
            ]);
        }

        // Pull a list of all the possible answers
        switch ($args['type']) {
            case PropertyType::Sale:
                $answers = Answer::query()
                    ->with('form')
                    ->whereHas('step', function ($query) {
                        $query
                            ->whereHas('section', function ($query) {
                                $query
                                    ->whereHas('form', function ($query) {
                                        // TODO: Filter down to the latest version of the form once that's available
                                        $query
                                            ->where('type', PropertyType::Sale)
                                            ->whereDoesntHave('conditions')
                                            ->whereNull('repeatable_answer_id');
                                    })
                                    ->whereDoesntHave('conditions');
                            })
                            ->whereDoesntHave('conditions');
                    })
                    ->whereDoesntHave('conditions');
                break;

            case PropertyType::Purchase:
                $answers = Answer::query()
                    ->with('form')
                    ->whereHas('step', function ($query) {
                        $query
                            ->whereHas('section', function ($query) {
                                $query
                                    ->whereHas('form', function ($query) {
                                        // TODO: Filter down to the latest version of the form once that's available
                                        $query
                                            ->where(function ($query) {
                                                $query
                                                    ->whereNot('ta_form_template', FormType::Giftor)
                                                    ->orWhereNull('ta_form_template');
                                            })
                                            ->where('type', PropertyType::Purchase)
                                            ->whereDoesntHave('conditions')
                                            ->whereNull('repeatable_answer_id');
                                    })
                                    ->whereDoesntHave('conditions');
                            })
                            ->whereDoesntHave('conditions');
                    })
                    ->whereDoesntHave('conditions');
                break;

            case PropertyType::Remortgage:
                $answers = Answer::query()
                    ->with('form')
                    ->whereHas('step', function ($query) {
                        $query
                            ->whereHas('section', function ($query) {
                                $query
                                    ->whereHas('form', function ($query) {
                                        // TODO: Filter down to the latest version of the form once that's available
                                        $query
                                            ->where('type', PropertyType::Remortgage)
                                            ->whereDoesntHave('conditions')
                                            ->whereNull('repeatable_answer_id');
                                    })
                                    ->whereDoesntHave('conditions');
                            })
                            ->whereDoesntHave('conditions');
                    })
                    ->whereDoesntHave('conditions');
                break;
        }

        $answers = $answers->get();

        $property->activeForms()->sync($answers->pluck('form.id'));

        // Get an array of form ids -> active form ids
        $activeForms = $property->activeForms()
            ->get()
            ->mapWithKeys(function ($form) {
                return [$form->id => $form->pivot->id];
            });

        // Insert placeholder answers for each possible answer
        $property
            ->providedAnswers()
            ->createMany($answers->map(function ($answer) use ($property, $invitedUser, $activeForms) {
                return [
                    'user_id' => $invitedUser->id,
                    'answer_id' => $answer->id,
                    'property_id' => $property->id,
                    'active_form_id' => $activeForms[$answer->form->id] ?? null,
                ];
            }));

        return $property;
    }
}
