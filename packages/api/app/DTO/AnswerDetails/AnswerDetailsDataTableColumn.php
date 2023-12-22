<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsDataTableColumn
{
    public string $name;

    public string $type;

    public ?string $placeholder;

    public string $pdfFieldSuffix;

    public function __construct(string $name, string $type, ?string $placeholder, string $pdfFieldSuffix)
    {
        $this->name = $name;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->pdfFieldSuffix = $pdfFieldSuffix;
    }
}
