<?php

namespace App\Jobs\Webhooks\YotiIdv;

use App\Enums\DocumentType;
use App\Enums\PaymentEventType;
use App\Enums\PropertyType;
use App\Events\PaymentEvent;
use App\Models\IdVerification;
use App\Notifications\GettingStartedThePropertyNotification;
use App\Services\PdfService\PdfService;
use App\Services\YotiIdvService\YotiIdvService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;
use Stripe\SubscriptionItem;

class SessionCompletionJob implements ShouldQueue
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

        // Get the correct record from the ID Verification table
        $idVerification = IdVerification::query()
            ->where('session_id', $payload['session_id'])
            ->first();

        if (! $idVerification) {
            return;
        }

        $idVerification->update([
            'id_verification_completed_at' => Carbon::now(),
        ]);

        /** @var YotiIdvService */
        $yotiIdv = app()->make(YotiIdvService::class);

        /** @var PdfService */
        $pdfService = app()->make(PdfService::class);

        /** @var StripeClient */
        $stripe = app()->make(StripeClient::class);

        // Pull data from Yoti
        $idvResult = $yotiIdv->getSessionResult($payload['session_id']);
        $idDoc = $idvResult->getResources()->getIdDocuments()[0];
        $docFieldsMediaId = $idDoc->getDocumentFields()->getMedia()->getId();

        $docFields = null;
        if ($docFieldsMediaId) {
            $docFields = json_decode($yotiIdv->getMedia($payload['session_id'], $docFieldsMediaId)->getContent(), true);
        }

        // Build the HTML
        $html = view('pdfs.yoti-idv', [
            'session_id' => $payload['session_id'],
            'id_documents' => $idvResult->getResources()->getIdDocuments(),
            'liveness' => $idvResult->getResources()->getLivenessCapture(),
            'checks' => collect($idvResult->getChecks()),
        ])->render();

        // Generate the PDF
        $pdf = $pdfService->render($html);

        // Add the document to the property
        $idVerification->property->map(function ($property) use ($pdf, $docFields, $payload) {
            $property
                ->addMediaFromString($pdf->content)
                ->usingFileName('idv-'.$payload['session_id'].'.pdf')
                ->usingName($docFields['full_name'].' Identity Verification')
                ->withCustomProperties([
                    'type' => DocumentType::Idv,
                    'yotiidv_session_id' => $payload['session_id'],
                ])
                ->toMediaCollection('documents');
            $property->users()->first()->notify(new GettingStartedThePropertyNotification);
        });

        // Get the Stripe customer
        $idVerification->property->map(function ($property) use ($stripe) {
            $customer = $stripe->customers->retrieve(
                $property->conveyancer->stripe_customer_id,
                [
                    'expand' => ['subscriptions'],
                ],
            );

            // Get the subscription item
            $productId = match ($property->type) {
                PropertyType::Sale => config('services.stripe.products.idv_seller'),
                PropertyType::Purchase => config('services.stripe.products.idv_buyer'),
                PropertyType::Remortgage => config('services.stripe.products.idv_remortgage'),
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
                    type: PaymentEventType::IDV,
                    conveyancer: $property->conveyancer,
                    propertyType: $property->type,
                    amount: $item->price->unit_amount,
                )
            );
        });
    }
}
