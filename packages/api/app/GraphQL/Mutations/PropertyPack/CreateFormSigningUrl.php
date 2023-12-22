<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\FormGroup;
use App\Enums\UserRole;
use App\Events\BillableAction;
use App\Models\Form;
use App\Models\ProvidedAnswer;
use App\Notifications\PaymentOnAccountNotification;
use App\Services\PdfService\PdfService;
use App\Services\ProtocolFormService\ProtocolFormService;
use App\Services\YotiSignService\YotiSignFile;
use App\Services\YotiSignService\YotiSignRecipient;
use App\Services\YotiSignService\YotiSignService;
use App\Services\YotiSignService\YotiSignSignature;
use Carbon\Carbon;

final class CreateFormSigningUrl
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var PdfService */
        $pdfService = app()->make(PdfService::class);

        /** @var YotiSignService */
        $signingService = app()->make(YotiSignService::class);

        // Only clients should be able to get the signing url
        if ($user->role !== UserRole::Client) {
            return null;
        }

        /** @var PdfService */
        $pdfService = app()->make(PdfService::class);

        // Get the property
        $property = $user
            ->properties()
            ->with('signedForms')
            ->find($args['property_id']);

        // Get the form
        $form = Form::with('sections.steps.answers')->find($args['form_id']);

        // If the property or form doesn't exist, return null
        if (! $property || ! $form) {
            return null;
        }

        // Check the status of the envelope
        if ($signedForm = $property->signedForms->firstWhere('id', $args['form_id'])) {
            $status = $signingService->getEnvelopeStatus($signedForm->pivot->letters_envelope_id);
            if ($status === 'ACTIVE') {
                return $signingService->getEmbedUrl($signedForm->pivot->letters_envelope_token);
            } else {
                return null;
            }
        }

        $answerIds = $form->sections->pluck('steps')->collapse()->pluck('answers')->collapse()->pluck('id');

        // Get all the answers
        $allProvidedAnswers = ProvidedAnswer::whereIn('answer_id', $answerIds)->get();

        $providedAnswers = [];
        foreach ($allProvidedAnswers as $providedAnswer) {
            if (! array_key_exists($providedAnswer->answer_id, $providedAnswers)) {
                $providedAnswers[$providedAnswer->answer_id] = [];
            }
            $providedAnswers[$providedAnswer->answer_id][] = $providedAnswer;
        }

        if ($form->group === FormGroup::Protocol) {
            // Generate the pdf from the protocol form service
            $pdf = ProtocolFormService::getPdf($form, $property);
        } elseif ($form->group === FormGroup::Enquiry) {
            // Generate the HTML
            $html = view('pdfs.enquiry-form', [
                'property' => $property,
                'form' => $form,
                'providedAnswers' => $providedAnswers,
            ])->render();

            // Generate the PDF
            $pdf = $pdfService->render($html);
        } else {
            // Other forms do not need to be signed
            return null;
        }

        // Create the envelope
        $envelope = $signingService->createEnvelope(
            name: preg_replace('/[\\\\:*?<>|]/', '', $form->name),
            recipients: [
                (new YotiSignRecipient)
                    ->withName($user->full_name)
                    ->withEmail($user->email),
            ],
            files: [
                (new YotiSignFile)
                    ->withPdf($pdf)
                    ->withName("$user->full_name $form->name")
                    ->withSignature(
                        (new YotiSignSignature)
                            ->withPageNumber($pdf->numberOfPages)
                            ->withX(optional($form->signature_coords)[0] ?? null)
                            ->withY(optional($form->signature_coords)[1] ?? null),
                    ),
            ],
        );

        // Trigger the billable event
        // event(new BillableAction($property));

        // Save the envelope ID
        $property->signedForms()->attach($form->id, [
            'letters_envelope_id' => $envelope['envelope_id'],
            'letters_envelope_token' => $envelope['recipients'][0]['token'],
        ]);

        $property->users()->updateExistingPivot($user->id, [
            'onboarding_forms_completed_at' => Carbon::now(),
        ]);

        $user->with('notificationPreferences')->where('id', $user->id)->first()->notify(new PaymentOnAccountNotification());

        return $signingService->getEmbedUrl($envelope['recipients'][0]['token']);
    }
}
