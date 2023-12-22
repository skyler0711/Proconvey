<?php

namespace App\Services\YotiSignService;

use App\Services\PdfService\PdfResult;

class YotiSignFile
{
    protected PdfResult $pdf;

    protected string $name;

    protected YotiSignSignature $signature;

    public function withPdf(PdfResult $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getPdf(): PdfResult
    {
        return $this->pdf;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withSignature(YotiSignSignature $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function getSignature(): YotiSignSignature
    {
        return $this->signature;
    }
}
