<?php

namespace App\GraphQL\Builders;

use App\Enums\PropertyType;
use App\Enums\UserStatus;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PropertiesBuilder
{
    public function __invoke(?Builder $builder, array $args): Builder
    {
        if (is_null($builder)) {
            $builder = Property::query();
        }

        /** @var \App\Models\User */
        $user = auth()->user();

        if (isset($args['search'])) {
            $search = $args['search'];
            $builder
                ->join('addresses', 'properties.id', '=', 'addresses.addressable_id')
                ->leftJoin('property_user', 'properties.id', '=', 'property_user.property_id')
                ->leftJoin('users', 'property_user.user_id', '=', 'users.id')
                ->where(function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where(DB::raw("CONCAT_WS(' ', line_1, line_2, city, postcode)"), 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', line_1, postcode)"), 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', line_1, city)"), 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', line_2, city)"), 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', line_2, postcode)"), 'LIKE', "%$search%");
                    })
                    ->orWhere(function ($query) use ($search) {
                        $query->where(DB::raw("CONCAT_WS(' ', users.first_name, users.last_name)"), 'LIKE', "%$search%")
                        ->orWhere('users.email', 'LIKE', "%$search%")
                        ->orWhere('users.phone', 'LIKE', "%$search%")
                        ->orWhere('users.job_role', 'LIKE', "%$search%");
                    })->orWhere(function ($query) use ($search) {
                        $query->where('properties.case_reference', 'LIKE', "%$search%");
                    });
                })
                ->where('addresses.addressable_type', '=', 'property')
                ->select('properties.*')
                ->distinct();
        }

        if (isset($args['filter_option'])) {
            switch ($args['filter_option']) {
                case UserStatus::Active:
                    $builder
                        ->whereNull('archived_at')
                        ->whereHas('users', function ($query) {
                            $query->whereNotNull('email_verified_at');
                        });
                    break;
                case UserStatus::Archived:
                    $builder
                        ->whereNotNull('properties.archived_at');
                    break;
                case UserStatus::NotAccepted:
                    $builder
                        ->whereHas('users', function ($query) {
                            $query->whereNull('email_verified_at');
                        });
                    break;
                case UserStatus::OnboardingInProgress:
                    $builder
                        ->whereNull('archived_at')
                        ->whereNull('property_user.onboarding_forms_completed_at');
                    break;
                case UserStatus::PackInProgress:
                    $builder
                        ->whereNull('archived_at')
                        ->whereNull('properties.pack_completed_at');
                    break;
                case UserStatus::Complete:
                    $builder
                        ->whereNull('archived_at')
                        ->whereNotNull('property_user.onboarding_forms_completed_at')
                        ->whereNotNull('properties.pack_completed_at');
                    break;
                case UserStatus::Sale:
                    $builder
                        ->whereNull('archived_at')
                        ->where('properties.type', PropertyType::Sale);
                    break;
                case UserStatus::Purchase:
                    $builder
                        ->whereNull('archived_at')
                        ->where('properties.type', PropertyType::Purchase);
                    break;
                case UserStatus::Remortgage:
                    $builder
                        ->whereNull('archived_at')
                        ->where('properties.type', PropertyType::Remortgage);
                    break;
                case 'all':
                    break;
            }
        }

        $builder->where('properties.conveyancer_id', $user->conveyancer_id);

        return $builder;
    }
}
