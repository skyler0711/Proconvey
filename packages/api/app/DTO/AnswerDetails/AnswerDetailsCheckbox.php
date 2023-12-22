<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsCheckbox
{
    public string $label;

    public ?string $pdfFormFieldName;

    public ?string $altValue;

    public ?string $altText;

    public function __construct(string $label, ?string $pdfFormFieldName, ?string $altValue, ?string $altText)
    {
        $this->label = $label;
        $this->pdfFormFieldName = $pdfFormFieldName;
        $this->altValue = $altValue; // Uses alt value when true
        $this->altText = $altText;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self($data['label'], optional($data)['pdfFormFieldName'], optional($data)['altValue'], optional($data)['altText']);
    }
}
