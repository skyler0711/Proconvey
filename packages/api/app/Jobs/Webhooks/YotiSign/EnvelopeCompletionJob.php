<?php

namespace App\Jobs\Webhooks\YotiSign;

use App\Enums\DocumentType;
use App\Enums\PaymentEventType;
use App\Enums\PropertyType;
use App\Events\PaymentEvent;
use App\Models\Property;
use App\Services\YotiSignService\YotiSignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;
use Stripe\SubscriptionItem;
use ZipArchive;

class EnvelopeCompletionJob implements ShouldQueue
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
        $payload = $this->webhookCall->payload;

        // Find the property this envelope is for
        /** @var Property $property */
        $property = Property::query()
            ->whereHas('users', function ($query) use ($payload) {
                $query->where('letters_envelope_id', $payload['envelope_id'])
                    ->orWhere(function ($query) use ($payload) {
                        $query->where('giftor_declaration_envelope_id', $payload['envelope_id']);
                    });
            })
            ->orWhereHas('signedForms', function ($query) use ($payload) {
                $query->where('letters_envelope_id', $payload['envelope_id']);
            })
            ->first();

        // If we can't find the property, we can't do anything
        if (! $property) {
            return;
        }

        // Get a list of files
        /** @var string[] $files */
        $files = array_map(fn ($f) => $f['name'], $payload['details']['files']);

        // Update the property_user table with the specific column
        if (collect($files)->contains(fn ($file) => DocumentType::fromFilename($file) === DocumentType::ClientCareLetter)) {
            $property->users()->where('letters_envelope_id', $payload['envelope_id'])->update([
                'onboarding_forms_completed_at' => now(),
            ]);
        }

        if (collect($files)->contains(fn ($file) => DocumentType::fromFilename($file) === DocumentType::GiftorDeclaration)) {
            $property->users()->where('giftor_declaration_envelope_id', $payload['envelope_id'])->update([
                'gifted_deposit_declaration_completed_at' => now(),
            ]);
        }

        /** @var YotiSignService $yotiSign */
        $yotiSign = app()->make(YotiSignService::class);

        // Download the zip file
        $zipFile = $yotiSign->getCompletedDocuments($payload['envelope_id']);
        $filename = tempnam(sys_get_temp_dir(), 'yotisign_');
        file_put_contents($filename, $zipFile);

        // Extract the zip
        $extractDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid();
        $zip = new ZipArchive();
        $zip->open($filename);
        $zip->extractTo($extractDir);
        $zip->close();

        // Add the documents to the property
        foreach ($files as $file) {
            $docType = DocumentType::fromFilename($file);

            if ($docType === DocumentType::Unknown) {
                continue;
            }

            $property->addMedia($extractDir.DIRECTORY_SEPARATOR.$file)
                ->withCustomProperties([
                    'type' => $docType,
                    'yotisign_envelope_id' => $payload['envelope_id'],
                ])
                ->toMediaCollection('documents');
        }

        /** @var StripeClient */
        $stripe = app()->make(StripeClient::class);

        // Get the Stripe customer
        $customer = $stripe->customers->retrieve(
            $property->conveyancer->stripe_customer_id,
            [
                'expand' => ['subscriptions'],
            ],
        );

        // Get the subscription item
        $productId = match ($property->type) {
            PropertyType::Sale => config('services.stripe.products.esig_seller'),
            PropertyType::Purchase => config('services.stripe.products.esig_buyer'),
            PropertyType::Remortgage => config('services.stripe.products.esig_remortgage'),
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

        // Record the payment
        event(
            new PaymentEvent(
                type: PaymentEventType::ESig,
                conveyancer: $property->conveyancer,
                propertyType: $property->type,
                amount: $item->price->unit_amount,
            )
        );
    }
}
