<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\FormType;
use App\Enums\PropertyUserRole;
use App\Enums\StepType;
use App\Enums\UserRole;
use App\Events\BillableAction;
use App\Services\PdfService\PdfService;
use App\Services\YotiSignService\YotiSignFile;
use App\Services\YotiSignService\YotiSignRecipient;
use App\Services\YotiSignService\YotiSignService;
use App\Services\YotiSignService\YotiSignSignature;
use Carbon\Carbon;

final class CreateGiftorDeclarationSigningUrl
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = auth()->user();

        $pdfService = app()->make(PdfService::class);

        $signingService = app()->make(YotiSignService::class);

        // Only clients should be able to get the signing url
        if ($user->role !== UserRole::Client) {
            return null;
        }

        // Get property
        $property = $user
            ->properties
            ->withPivot('giftor_declaration_envelope_id', 'giftor_declaration_envelope_token', 'role')
            ->find($args['property_id']);

        if ($property->pivot->role !== PropertyUserRole::Giftor) {
            return null;
        }

        // Get active form
        $activeForm = $property->activeForms()->firstWhere('ta_form_template', FormType::GettingStarted);

        // If the property doesn't exist, return null
        if (! $property) {
            return null;
        }

        // Check the status of the envelope
        if ($property->pivot->giftor_declaration_envelope_id) {
            $status = $signingService->getEnvelopeStatus($property->pivot->giftor_declaration_envelope_id);
            if ($status === 'ACTIVE') {
                return $signingService->getEmbedUrl($property->pivot->giftor_declaration_envelope_token);
            } else {
                return null;
            }
        }

        // Get answers for steps
        $buyerDetailsStep = $activeForm->sections()->firstWhere('name', 'The Buyers')->steps()->firstWhere('type', StepType::BuyerExpanded);
        $giftorDetailsStep = $activeForm->sections()->firstWhere('name', 'Purchase Funds')->steps()->firstWhere('type', StepType::BuyerGiftor);

        // Get giftor details
        $giftors = $giftorDetailsStep->getCompiledAnswer($property);
        $giftor = array_filter($giftors, function ($item) use ($user) {
            return $item['email'] === $user->email;
        })[0];

        // Build buyer names string
        $compiledAnswer = $buyerDetailsStep->getCompiledAnswer($property);
        $buyerNames = array_map(function ($buyer) {
            return $buyer['name'];
        }, $compiledAnswer);

        $buyers = implode(', ', $buyerNames);
        $lastCommaPos = strrpos($buyers, ', ');
        $buyers = substr_replace($buyers, ' & ', $lastCommaPos, 2);

        $html = view('pdfs.giftor-declaration', [
            'name' => $user->fullName,
            'address_line_1' => $user->address->line_1,
            'address_line_2' => $user->address?->line_2,
            'address_city' => $user->address->city,
            'address_postcode' => $user->address->postcode,
            'date' => Carbon::now()->format('Y-m-d'),
            'amount' => $giftor['amount_being_loaned'],
            'buyers' => $buyers,
            'property_address' => $property->address->singleLine,
            'reference' => $property->case_reference,
        ]);

        $pdf = $pdfService->render($html);

        // Create the envelope
        $envelope = $signingService->createEnvelope(
            name: 'Gifted Giftor Declaration',
            recipients: [
                (new YotiSignRecipient)
                    ->withName($user->full_name)
                    ->withEmail($user->email),
            ],
            files: [
                (new YotiSignFile)
                    ->withPdf($pdf)
                    ->withName("$user->full_name Giftor Declaration")
                    ->withSignature(
                        (new YotiSignSignature)
                            ->withPageNumber($pdf->numberOfPages)
                            ->withX(0.11)
                            ->withY(0.81),
                    ),
            ],
        );

        // Save the envelope ID
        $property->users()->updateExistingPivot($user->id, [
            'giftor_declaration_envelope_id' => $envelope['envelope_id'],
            'giftor_declaration_envelope_token' => $envelope['recipients'][0]['token'],
        ]);

        // Trigger the billable event
        event(new BillableAction($property));

        return $signingService->getEmbedUrl($envelope['recipients'][0]['token']);
    }
}
