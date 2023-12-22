<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsDataTableRow
{
    public string $name;

    public string $pdfFieldPrefix;

    public function __construct(string $name, string $pdfFieldPrefix)
    {
        $this->name = $name;
        $this->pdfFieldPrefix = $pdfFieldPrefix;
    }
}
