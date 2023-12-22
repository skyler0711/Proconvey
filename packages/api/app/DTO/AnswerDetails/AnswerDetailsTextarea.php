<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsTextarea
{
    public ?string $label;

    public ?string $placeholder;

    public ?string $pdfFormFieldName;

    public function __construct(?string $label, ?string $placeholder, ?string $pdfFormFieldName)
    {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->pdfFormFieldName = $pdfFormFieldName;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(optional($data)['label'], optional($data)['placeholder'], optional($data)['pdfFormFieldName']);
    }
}
