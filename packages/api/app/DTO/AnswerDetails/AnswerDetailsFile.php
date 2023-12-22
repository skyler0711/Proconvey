<?php

namespace App\DTO\AnswerDetails;

use App\Enums\FileTextAnswerTypes;
use Illuminate\Support\Arr;

final class AnswerDetailsFile
{
    public ?string $label;

    public ?string $pdfFieldPrefix;

    public ?array $textAnswers;

    public function __construct(?string $label, ?string $pdfFieldPrefix, ?string $pdfFormField, ?array $textAnswers)
    {
        $this->label = $label;
        $this->pdfFieldPrefix = $pdfFieldPrefix; // Add this to files when you want uploads to effect 'enclosed' or 'attached' checkboxes in forms
        $this->pdfFormField = $pdfFormField; // For when an upload effects a text box, this tells the textAnswers where to go
        $this->textAnswers = $textAnswers; // Array of text answers keyed by the FIleTextAnswerTypes enum
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(optional($data)['label'], optional($data)['pdfFieldPrefix'], optional($data)['pdfFormField'], Arr::get($data, 'textAnswers', []));
    }
}
