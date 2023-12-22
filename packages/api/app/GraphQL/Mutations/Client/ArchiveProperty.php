<?php

namespace App\GraphQL\Mutations\Client;

use App\Models\Property;
use Carbon\Carbon;

final class ArchiveProperty
{
    public function __invoke($_, array $args)
    {
        $property = Property::find($args['id']);
        $property->archived_at = Carbon::now();
        $property->save();

        return $property;
    }
}
