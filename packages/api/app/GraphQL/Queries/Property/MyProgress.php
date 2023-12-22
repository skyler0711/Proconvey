<?php

namespace App\GraphQL\Queries\Property;

use App\Enums\DocumentType;
use App\Enums\PropertyUserRole;
use App\Enums\UserRole;
use App\Models\Property;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class MyProgress
{
    /**
     * @param  array{}  $args
     */
    public function __invoke(Property $property, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $idForConveyancer = $user->idVerification()->where('conveyancer_id', $property->conveyancer_id)->first();

        // Get the property
        $pivot = $property
            ->users()
            ->where('user_id', auth()->id())
            ->withPivot(
                'payment_on_account_completed_at',
                'onboarding_forms_completed_at',
                'gifted_deposit_declaration_completed_at',
                'sof_completed_at',
            )
            ->first()
            ?->pivot;

        // Get which fields are selected
        $rootSelections = collect($resolveInfo->fieldNodes)
            ->first(fn ($node) => $node->name->value === 'my_progress');

        if (! $rootSelections) {
            $rootSelections = $resolveInfo->fieldNodes[0];
        }

        $rootSelections = $rootSelections
            ->selectionSet
            ->selections;

        $selectedFields = collect($rootSelections)
            ->map(fn ($node) => $node->name->value);

        // Setup the return object
        $returnObject = [];

        // Get the onboarding letters status
        if ($selectedFields->contains('onboarding_letters')) {
            $returnObject['onboarding_letters'] = [
                'required' => $property->letters_required,
                'completed' => ! is_null($pivot->onboarding_forms_completed_at),
            ];
        }

        // Get the IDV status
        if ($selectedFields->contains('idv')) {
            $returnObject['idv'] = [
                'required' => $property->id_check_required,
                'mobile_connected' => ! is_null($idForConveyancer->mobile_connected_at),
                'completed' => $idForConveyancer->id_verification_completed_at,
            ];
        }

        // Get the payment status
        if ($selectedFields->contains('payment')) {
            $returnObject['payment'] = [
                'required' => in_array($pivot->role, [PropertyUserRole::Attorney, PropertyUserRole::Deputy])
                    ? false
                    : $property->payment_required,
                'paid' => ! is_null($pivot->payment_on_account_completed_at),
            ];
        }

        if ($selectedFields->contains('giftor_deposit_declaration')) {
            $returnObject['giftor_deposit_declaration'] = [
                'required' => in_array($pivot->role, [PropertyUserRole::Giftor]),
                'completed' => ! is_null($pivot->gifted_deposit_declaration_completed_at),
            ];
        }

        // Get the SOF status
        if ($selectedFields->contains('sof')) {
            $returnObject['sof'] = [
                'required' => in_array($pivot->role, [PropertyUserRole::Attorney, PropertyUserRole::Deputy])
                    ? false
                    : $property->sof_check_required,
                'completed' => ! is_null($pivot->sof_completed_at),
                'files' => $property
                    ->media()
                    ->where('custom_properties->type', DocumentType::SofCheck)
                    ->get(),
            ];
        }

        // Get the provided answers
        if ($selectedFields->contains('provided_answers')) {
            $returnObject['provided_answers'] = $property
                ->providedAnswers()
                ->with('answer')
                ->when($user->role !== UserRole::Conveyancer, fn ($query) => $query->where('user_id', $user->id))
                ->get();
        }

        // Get the pack progress
        if ($selectedFields->contains('pack_progress')) {
            $returnObject['pack_progress'] = [
                'completed' => false,
            ];
        }

        return $returnObject;
    }
}
