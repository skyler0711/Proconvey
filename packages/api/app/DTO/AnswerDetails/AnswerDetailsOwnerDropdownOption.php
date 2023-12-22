<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsOwnerDropdownOption
{
    public string $value;

    public ?string $pdfFormFieldName;

    public ?string $altText;

    public function __construct(string $value, ?string $pdfFormFieldName, ?string $altText)
    {
        $this->value = $value;
        $this->pdfFormFieldName = $pdfFormFieldName;
        $this->altText = $altText;
    }
}
