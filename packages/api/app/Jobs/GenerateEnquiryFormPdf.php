<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\Form;
use App\Models\Property;
use App\Models\ProvidedAnswer;
use App\Services\PdfService\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEnquiryFormPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $formId;

    public int $propertyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $formId, int $propertyId)
    {
        $this->formId = $formId;
        $this->propertyId = $propertyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var PdfService */
        $pdfService = app()->make(PdfService::class);

        // Get the property
        $property = Property::find($this->propertyId);

        // Get the form
        $form = Form::with('sections.steps.answers')->find($this->formId);
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

        // Generate the HTML
        $html = view('pdfs.enquiry-form', [
            'property' => $property,
            'form' => $form,
            'providedAnswers' => $providedAnswers,
        ])->render();

        // Generate the PDF
        $pdf = $pdfService->render($html);

        // Add the document to the property
        $property
            ->addMediaFromString($pdf->content)
            ->usingFileName($form->name.'.pdf')
            ->usingName($form->name)
            ->withCustomProperties([
                'type' => DocumentType::Form,
                'form_id' => $form->id,
            ])
            ->toMediaCollection('documents');
    }
}
