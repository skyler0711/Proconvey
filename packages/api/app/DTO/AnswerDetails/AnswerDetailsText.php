<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsText
{
    public ?string $label;

    public ?string $placeholder;

    public ?string $pdfFormFieldName;

    public ?string $altText;

    public function __construct(?string $label, ?string $placeholder, ?string $pdfFormFieldName, ?string $altText)
    {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->pdfFormFieldName = $pdfFormFieldName;
        $this->altText = $altText;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(optional($data)['label'], optional($data)['placeholder'], optional($data)['pdfFormFieldName'], optional($data)['altText']);
    }
}
