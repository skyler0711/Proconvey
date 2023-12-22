<?php

namespace App\GraphQL\Mutations\Conveyancer;

use Stripe\StripeClient;

final class CreateSetupIntent
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

        $setupIntent = $stripe->setupIntents->create([
            'customer' => $conveyancer->stripe_customer_id,
            'usage' => 'off_session',
        ]);

        return $setupIntent->client_secret;
    }
}
