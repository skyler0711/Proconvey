<?php

namespace App\Listeners;

use App\Enums\PaymentEventType;
use App\Enums\PropertyType;
use App\Events\BillableAction;
use App\Events\PaymentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Stripe\StripeClient;
use Stripe\SubscriptionItem;

class BillForPack implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BillableAction $event)
    {
        // If this property has already been billed, bail
        if ($event->property->billed_for_at) {
            return;
        }

        /** @var StripeClient */
        $stripe = app()->make(StripeClient::class);

        // Get the customer
        $customer = $stripe->customers->retrieve(
            $event->property->conveyancer->stripe_customer_id,
            [
                'expand' => ['subscriptions'],
            ],
        );

        // Get the subscription item
        $productId = match ($event->property->type) {
            PropertyType::Sale => config('services.stripe.products.packs_seller'),
            PropertyType::Purchase => config('services.stripe.products.packs_buyer'),
            PropertyType::Remortgage => config('services.stripe.products.packs_remortgage'),
        };

        $item = collect($customer->subscriptions->first()->items->data)
            ->first(fn (SubscriptionItem $i) => $i->price->product === $productId);

        // Add the item if needed
        if (! $item) {
            $price = $stripe->products->retrieve($productId)->default_price;
            $item = $stripe->subscriptionItems->create([
                'subscription' => $customer->subscriptions->first()->id,
                'price' => $price,
            ]);
        }

        // Increment the quantity
        $stripe->subscriptionItems->createUsageRecord(
            $item->id,
            [
                'quantity' => 1,
            ]
        );

        // Update billed at date
        $event->property->update([
            'billed_for_at' => now(),
        ]);

        // Record the payment
        event(
            new PaymentEvent(
                type: PaymentEventType::ClientPack,
                conveyancer: $event->property->conveyancer,
                propertyType: $event->property->type,
                amount: $item->price->unit_amount,
            )
        );
    }
}
