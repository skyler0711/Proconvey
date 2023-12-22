<?php

namespace App\Observers;

use App\Models\Conveyancer;
use App\Models\User;
use Stripe\StripeClient;

class ConveyancerObserver
{
    public function creating(Conveyancer $conveyancer)
    {
        /** @var StripeClient */
        $stripe = app(StripeClient::class);

        /** @var User */
        $user = auth()->user();

        $user?->update([
            'business_created_at' => now(),
        ]);

        if (is_null($conveyancer->stripe_customer_id)) {
            // Create a new customer in Stripe
            $customer = $stripe->customers->create([
                ...(
                    $conveyancer->address
                        ? [
                            'address' => [
                                'line1' => $conveyancer->address->line1,
                                'line2' => $conveyancer->address->line2,
                                'city' => $conveyancer->address->city,
                                'postal_code' => $conveyancer->address->postcode,
                                'country' => 'GB',
                            ],
                        ]
                        : []
                ),
                'email' => $user->email,
                'name' => $conveyancer->name,
            ]);

            // Store the customer ID in the database
            $conveyancer->stripe_customer_id = $customer->id;

            // Get the default price for the products
            $prices = collect();
            foreach (config('services.stripe.products') as $key => $productId) {
                $prices->add($stripe->products->retrieve($productId)->default_price);
            }

            //Create a new subscription in Stripe
            $stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => $prices->map(fn ($price) => ['price' => $price])->toArray(),
            ]);
        }
    }

    public function updated(Conveyancer $conveyancer)
    {
        /** @var StripeClient */
        $stripe = app(StripeClient::class);

        // Update the customer in Stripe
        // Don't update the email address as this is recorded as a billing address seperately
        $stripe->customers->update(
            $conveyancer->stripe_customer_id,
            [
                ...(
                    $conveyancer->address
                        ? [
                            'address' => [
                                'line1' => $conveyancer->address->line_1,
                                'line2' => $conveyancer->address->line_2,
                                'city' => $conveyancer->address->city,
                                'postal_code' => $conveyancer->address->postcode,
                                'country' => 'GB',
                            ],
                        ]
                        : []
                ),
                'name' => $conveyancer->name,
            ]
        );
    }
}
