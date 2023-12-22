<?php

namespace App\GraphQL\Mutations\Conveyancer;

use Stripe\StripeClient;

final class UpdateBillingEmail
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

        $stripe->customers->update(
            $conveyancer->stripe_customer_id,
            [
                'email' => $args['email'],
            ]
        );

        return true;
    }
}
