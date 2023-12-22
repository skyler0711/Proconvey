<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Models\IdVerification;
use Carbon\Carbon;

final class IdvMobileConnected
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $idVerification = IdVerification::query()
            ->where('session_id', $args['session_id'])
            ->first();

        if (! $idVerification) {
            return null;
        }

        $idVerification->update([
            'mobile_connected_at' => $args['reset'] ? null : Carbon::now(),
        ]);

        return true;
    }
}
