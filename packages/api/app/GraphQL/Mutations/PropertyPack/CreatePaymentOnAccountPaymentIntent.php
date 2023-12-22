<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Notifications\IDVerificationNotification;
use Carbon\Carbon;
use Stripe\StripeClient;

final class CreatePaymentOnAccountPaymentIntent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get the property
        $property = $user
            ->properties()
            ->find($args['property_id']);

        // If the property doesn't exist, return null
        if (! $property) {
            return null;
        }

        // Handle if payment on account is not required
        if (! $property->payment_required) {
            return null;
        }

        /** @var \Stripe\StripeClient */
        $stripe = app()->make(StripeClient::class);

        // Check if this property already has a payment intent
        if ($property->payment_id) {
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $property->payment_id,
                null,
                [
                    'stripe_account' => $property->conveyancer->stripe_account_id,
                ],
            );

            if ($paymentIntent->status === 'succeeded') {
                return null;
            }

            return $paymentIntent->client_secret;
        }

        // Create the payment intent
        $paymentIntent = $stripe->paymentIntents->create(
            [
                'amount' => $property->payment_amount,
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'description' => 'Payment on account for '.$property->address->single_line.' (via ProConvey)',
            ],
            [
                'stripe_account' => $property->conveyancer->stripe_account_id,
            ],
        );

        // Update the property with the payment intent ID
        $property->update([
            'payment_id' => $paymentIntent->id,
        ]);

        $property->users()->updateExistingPivot($user->id, [
            'payment_on_account_completed_at' => Carbon::now(),
        ]);

        $user->with('notificationPreferences')->where('id', $user->id)->first()->notify(new IDVerificationNotification());

        return $paymentIntent->client_secret;
    }
}
