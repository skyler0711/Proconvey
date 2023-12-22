<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\Form;
use App\Models\Property;
use App\Services\ProtocolFormService\ProtocolFormService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateProtocolFormPdf implements ShouldQueue
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
        // Get the property
        $property = Property::find($this->propertyId);

        // Get the Form
        $form = Form::with('sections.steps.answers')->find($this->formId);

        // Get the PDF
        $result = ProtocolFormService::getPdf($form, $property);

        // // Add the document to the property
        $property
            ->addMediaFromString($result->content)
            ->usingFileName($form->name.'.pdf')
            ->usingName($form->name)
            ->withCustomProperties([
                'type' => DocumentType::Form,
                'form_id' => $form->id,
            ])
            ->toMediaCollection('documents');
    }
}
