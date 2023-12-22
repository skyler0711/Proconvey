<?php

namespace App\GraphQL\Mutations\Conveyancer;

use Stripe\StripeClient;

final class CompleteSetupIntent
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

        // Get all the payment methods for this customer
        $paymentMethods = $stripe->paymentMethods->all([
            'customer' => $conveyancer->stripe_customer_id,
        ]);

        // Delete the old ones
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod->id === $args['payment_method']) {
                continue;
            }
            $paymentMethod->detach();
        }

        // Update the billing email for the customer
        $stripe->customers->update(
            $conveyancer->stripe_customer_id,
            [
                'email' => $args['email'],
                'invoice_settings' => [
                    'default_payment_method' => $args['payment_method'],
                ],
            ]
        );

        return true;
    }
}
