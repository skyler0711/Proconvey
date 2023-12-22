<?php

namespace App\Listeners;

use App\Events\PaymentEvent;
use App\Models\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordPaymentLog implements ShouldQueue
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
    public function handle(PaymentEvent $event)
    {
        PaymentLog::create([
            'type' => $event->type,
            'conveyancer_id' => $event->conveyancer->id,
            'property_type' => $event->propertyType,
            'amount' => $event->amount,
        ]);
    }
}
