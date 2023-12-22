<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsDropdownOption
{
    public string $value;

    public ?string $pdfFormFieldName;

    public function __construct(string $value, ?string $pdfFormFieldName)
    {
        $this->value = $value;
        $this->pdfFormFieldName = $pdfFormFieldName;
    }
}
