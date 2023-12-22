<?php

namespace App\GraphQL\Mutations\Conveyancer;

use Stripe\StripeClient;

final class UpdateStripeCode
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Conveyancer */
        $conveyancer = auth()->user()->conveyancer;

        /** @var \Stripe\StripeClient */
        $stripe = app()->make(StripeClient::class);

        $response = $stripe->oauth->token([
            'grant_type' => 'authorization_code',
            'code' => $args['code'],
        ]);

        $conveyancer->stripe_account_id = $response->stripe_user_id;
        $conveyancer->save();

        return true;
    }
}
