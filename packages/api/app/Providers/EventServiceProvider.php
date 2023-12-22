<?php

namespace App\Providers;

use App\Events\BillableAction;
use App\Events\PaymentEvent;
use App\Listeners\BillForPack;
use App\Listeners\RecordPaymentLog;
use App\Models\Address;
use App\Models\Conveyancer;
use App\Models\Step;
use App\Models\User;
use App\Observers\AddressObserver;
use App\Observers\ConveyancerObserver;
use App\Observers\StepObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        BillableAction::class => [
            BillForPack::class,
        ],

        PaymentEvent::class => [
            RecordPaymentLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Step::observe(StepObserver::class);
        Conveyancer::observe(ConveyancerObserver::class);
        Address::observe(AddressObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
