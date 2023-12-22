<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\Form;
use App\Models\Property;
use App\Models\Section;
use App\Services\PdfService\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class GenerateSaleOverviewPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Property $property;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
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
        $property = $this->property->load(['providedAnswers', 'forms.sections.steps.answers']);

        // Generate the HTML
        $html = view('pdfs.sale-overview', [
            'property' => $property,
            'providedAnswers' => $property->providedAnswers,
            'forms' => $property->forms,
            'allSteps' => $property->forms->reduce(function (Collection $carry, Form $form) {
                return $carry->merge($form->sections->reduce(function (Collection $carry, Section $section) {
                    return $carry->merge($section->steps);
                }, collect()));
            }, collect()),
            'ownerExtraInformation' => $property->getRepresentatives(),
            'details' => $property->getOverviewPdfDetails(),
        ])->render();

        // Generate the PDF
        $pdf = $pdfService->render($html);

        // Add the document to the property
        $property
            ->addMediaFromString($pdf->content)
            ->usingFileName('Sale Overview.pdf')
            ->usingName('Sale Overview')
            ->withCustomProperties([
                'type' => DocumentType::Form,
            ])
            ->toMediaCollection('documents');
    }
}
