<?php

namespace App\Services\YotiSignService;

class YotiSignSignature
{
    const DEFAULT_X = 0.1;

    const DEFAULT_Y = 0.9;

    protected int $pageNumber;

    protected ?float $x;

    protected ?float $y;

    public function withPageNumber(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function withX(?float $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getX(): float
    {
        return $this->x ?? self::DEFAULT_X;
    }

    public function withY(?float $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getY(): float
    {
        return $this->y ?? self::DEFAULT_Y;
    }
}
