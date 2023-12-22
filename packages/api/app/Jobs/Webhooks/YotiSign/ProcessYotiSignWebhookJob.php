<?php

namespace App\Jobs\Webhooks\YotiSign;

use App\Exceptions\WebhookFailed;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessYotiSignWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        if (! isset($this->webhookCall->payload['subscription_name']) || $this->webhookCall->payload['subscription_name'] === '') {
            throw WebhookFailed::missingSubscription($this->webhookCall);
        }

        $jobClass = $this->determineJobClass($this->webhookCall->payload['subscription_name']);

        if ($jobClass === '') {
            return;
        }

        if (! class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $this->webhookCall);
        }

        dispatch(new $jobClass($this->webhookCall));
    }

    protected function determineJobClass(string $eventType): string
    {
        $jobConfigKey = str_replace('.', '_', $eventType);

        $configs = collect(config('webhook-client.configs'));
        $config = $configs->firstWhere('name', $this->webhookCall->name);

        return $config['jobs'][$jobConfigKey];
    }
}
