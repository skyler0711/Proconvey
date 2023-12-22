<?php

namespace App\Jobs\Webhooks\Stripe;

use App\Models\Conveyancer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Event;

class AccountApplicationDeauthorized implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var \Stripe\App */
        $application = Event::constructFrom($this->webhookCall->payload);

        $conveyancer = Conveyancer::firstWhere('stripe_account_id', $application->account);
        $conveyancer->stripe_account_id = null;
        $conveyancer->save();
    }
}
