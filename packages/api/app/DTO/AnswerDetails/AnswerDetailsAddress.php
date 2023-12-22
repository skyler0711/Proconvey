<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsAddress
{
    public ?string $label;

    public ?string $pdfFormFieldName;

    public function __construct(?string $label, ?string $pdfFormFieldName)
    {
        $this->label = $label;
        $this->pdfFormFieldName = $pdfFormFieldName;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(optional($data)['label'], optional($data)['pdfFormFieldName']);
    }
}
