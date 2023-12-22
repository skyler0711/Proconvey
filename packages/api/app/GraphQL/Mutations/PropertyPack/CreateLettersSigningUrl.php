<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\UserRole;
use App\Events\BillableAction;
use App\Services\OnboardingLettersService\OnboardingLettersService;
use App\Services\PdfService\PdfService;
use App\Services\YotiSignService\YotiSignFile;
use App\Services\YotiSignService\YotiSignRecipient;
use App\Services\YotiSignService\YotiSignService;
use App\Services\YotiSignService\YotiSignSignature;

final class CreateLettersSigningUrl
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var OnboardingLettersService */
        $onboardingService = app()->make(OnboardingLettersService::class);

        /** @var PdfService */
        $pdfService = app()->make(PdfService::class);

        /** @var YotiSignService */
        $signingService = app()->make(YotiSignService::class);

        // Only clients should be able to get the signing url
        if ($user->role !== UserRole::Client) {
            return null;
        }

        // Get the property
        $property = $user
            ->properties()
            ->withPivot('letters_envelope_id', 'letters_envelope_token')
            ->find($args['property_id']);

        // If the property doesn't exist, return null
        if (! $property) {
            return null;
        }

        // Check the status of the envelope
        if ($property->pivot->letters_envelope_id) {
            $status = $signingService->getEnvelopeStatus($property->pivot->letters_envelope_id);
            if ($status === 'ACTIVE') {
                return $signingService->getEmbedUrl($property->pivot->letters_envelope_token);
            } else {
                return null;
            }
        }

        // Generate the HTML
        $contentMatch = match ($property->type) {
            'Sale' => $property->conveyancer->client_care_letter_sale ?? '',
            'Purchase' => $property->conveyancer->client_care_letter_purchase ?? '',
            'Remortage' => $property->conveyancer->client_care_letter_remortgage ?? '',
        };

        $clientCareHtml = $onboardingService->getHtml(
            content: $contentMatch,
            user: $user,
            property: $property,
        );

        $termsHtml = $onboardingService->getHtml(
            content: $property->conveyancer->terms_and_conditions ?? '',
            user: $user,
            property: $property,
        );

        // Generate the pdfs
        $clientCarePdf = $pdfService->render($clientCareHtml);
        $termsPdf = $pdfService->render($termsHtml);

        // Create the envelope
        $envelope = $signingService->createEnvelope(
            name: 'Client Care Letter and Terms & Conditions',
            recipients: [
                (new YotiSignRecipient)
                    ->withName($user->full_name)
                    ->withEmail($user->email),
            ],
            files: [
                (new YotiSignFile)
                    ->withPdf($clientCarePdf)
                    ->withName("$user->full_name Client Care Letter")
                    ->withSignature(
                        (new YotiSignSignature)
                            ->withPageNumber($clientCarePdf->numberOfPages)
                    ),
                (new YotiSignFile)
                    ->withPdf($termsPdf)
                    ->withName("$user->full_name Terms & Conditions")
                    ->withSignature(
                        (new YotiSignSignature)
                            ->withPageNumber($termsPdf->numberOfPages)
                    ),
            ],
        );

        // Trigger the billable event
        event(new BillableAction($property));

        // Save the envelope id
        $property->users()->updateExistingPivot($user->id, [
            'letters_envelope_id' => $envelope['envelope_id'],
            'letters_envelope_token' => $envelope['recipients'][0]['token'],
        ]);

        return $signingService->getEmbedUrl($envelope['recipients'][0]['token']);
    }
}
