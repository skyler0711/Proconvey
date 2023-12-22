<?php

return [
    'configs' => [
        // Stripe webhooks are handled directly, not here

        [
            'name' => 'yotisign',
            'signing_secret' => null,
            'signature_header_name' => null,
            'signature_validator' => \App\Jobs\Webhooks\YotiSign\YotiSignSignatureValidator::class,
            'webhook_profile' => \Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile::class,
            'webhook_response' => \Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo::class,
            'webhook_model' => \Spatie\WebhookClient\Models\WebhookCall::class,
            'store_headers' => [],
            'process_webhook_job' => \App\Jobs\Webhooks\YotiSign\ProcessYotiSignWebhookJob::class,
            'jobs' => [
                'envelope_completion' => \App\Jobs\Webhooks\YotiSign\EnvelopeCompletionJob::class,
            ],
        ],

        [
            'name' => 'yotiidv',
            'signing_secret' => null,
            'signature_header_name' => null,
            'signature_validator' => \App\Jobs\Webhooks\YotiIdv\YotiIdvSignatureValidator::class,
            'webhook_profile' => \Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile::class,
            'webhook_response' => \Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo::class,
            'webhook_model' => \Spatie\WebhookClient\Models\WebhookCall::class,
            'store_headers' => [],
            'process_webhook_job' => \App\Jobs\Webhooks\YotiIdv\ProcessYotiIdvWebhookJob::class,
            'jobs' => [
                'session_completion' => \App\Jobs\Webhooks\YotiIdv\SessionCompletionJob::class,
            ],
        ],
    ],

    /*
     * The integer amount of days after which models should be deleted.
     *
     * 7 deletes all records after 1 week. Set to null if no models should be deleted.
     */
    'delete_after_days' => 30,
];
