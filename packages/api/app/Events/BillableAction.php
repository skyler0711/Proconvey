<?php

namespace App\Events;

use App\Models\Property;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillableAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Property $property;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }
}
