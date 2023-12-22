<?php

namespace App\GraphQL\Queries\Property;

use App\Enums\FormType;
use App\Enums\PropertyUserRole;
use App\Enums\UserRole;
use App\Models\Property;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ActiveForms
{
    /**
     * @param  array{}  $args
     */
    public function __invoke(Property $property, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $activeForms = $property->activeForms();

        switch ($user->role) {
            case UserRole::Admin:
            case UserRole::Conveyancer:
                return $activeForms->get();

            case UserRole::Client:
                $propertyUser = $property
                  ->users()
                  ->where('user_id', $user->id)
                  ->first()
                  ->pivot;

                switch ($propertyUser->role) {
                    case PropertyUserRole::Buyer:
                    case PropertyUserRole::Owner:
                    case PropertyUserRole::Remortgager:
                        return $propertyUser->is_primary_user
                            ? $activeForms->where(function ($query) {
                                $query
                                    ->whereNot('ta_form_template', FormType::Giftor)
                                    ->orWhereNull('ta_form_template');
                            })->get()
                            : [];

                    case PropertyUserRole::Giftor:
                        return $activeForms
                          ->where('ta_form_template', FormType::Giftor)
                          ->get();

                    case PropertyUserRole::Attorney:
                    case PropertyUserRole::Executor:
                    default:
                        return [];
                }

            default:
                return [];
        }
    }
}
