<?php

namespace App\Observers;

use App\Models\Address;
use App\Models\Conveyancer;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class AddressObserver
{
    public function saving(Address $address)
    {
        // Convert the postcode to uppercase
        $address->postcode = Str::upper($address->postcode);

        // Update the address in Stripe if it's connected to a conveyancer
        if ($address->addressable_type === 'conveyancer') {
            /** @var Conveyancer */
            $conveyancer = $address->addressable;

            /** @var StripeClient */
            $stripe = app(StripeClient::class);

            $stripe->customers->update(
                $conveyancer->stripe_customer_id,
                [
                    'address' => [
                        'line1' => $address->line_1,
                        'line2' => $address->line_2,
                        'city' => $address->city,
                        'postal_code' => $address->postcode,
                        'country' => 'GB',
                    ],
                ]
            );
        }
    }
}
