<?php

namespace App\Services\PdfService;

final class PdfResult
{
    public string $content;

    public int $numberOfPages;

    public function __construct(string $content, int $numberOfPages)
    {
        $this->content = $content;
        $this->numberOfPages = $numberOfPages;
    }
}
