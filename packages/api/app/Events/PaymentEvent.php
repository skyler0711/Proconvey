<?php

namespace App\Events;

use App\Models\Conveyancer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $type;

    public Conveyancer $conveyancer;

    public string $propertyType;

    public int $amount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $type, Conveyancer $conveyancer, string $propertyType, int $amount)
    {
        $this->type = $type;
        $this->conveyancer = $conveyancer;
        $this->propertyType = $propertyType;
        $this->amount = $amount;
    }
}
