<?php

namespace App\GraphQL\Mutations\Conveyancer;

final class UpdateIDProvider
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Conveyancer */
        $conveyancer = auth()->user()->conveyancer;

        $conveyancer->id_provider = $args['provider'];

        $conveyancer->save();

        return true;
    }
}
