<?php

namespace App\Jobs\Webhooks\YotiIdv;

use App\Exceptions\WebhookFailed;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessYotiIdvWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        if (! isset($this->webhookCall->payload['topic']) || $this->webhookCall->payload['topic'] === '') {
            throw WebhookFailed::missingSubscription($this->webhookCall);
        }

        $jobClass = $this->determineJobClass($this->webhookCall->payload['topic']);

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
