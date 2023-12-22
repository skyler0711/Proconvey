<?php

namespace App\GraphQL\Builders;

use App\Enums\UserRole;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;

class GetClientPropertiesBuilder
{
    public function __invoke(?Builder $builder, array $args): Builder
    {
        if (is_null($builder)) {
            $builder = Property::query();
        }

        /** @var \App\Models\User */
        $user = auth()->user();

        $builder
            ->whereHas('users', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->when(auth()->user()->role !== UserRole::Conveyancer, fn ($query) => $query->whereNull('archived_at'));

        return $builder;
    }
}
