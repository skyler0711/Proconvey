<?php

namespace App\DTO\Subscription;

use Carbon\Carbon;

final class Invoice
{
    public string $plan_name;

    public ?string $number;

    public int $amount;

    public Carbon $date;

    public string $status;

    public ?string $pdf_url;

    public function __construct(string $plan_name, ?string $number, int $amount, Carbon $date, string $status, ?string $pdf_url)
    {
        $this->plan_name = $plan_name;
        $this->number = $number;
        $this->amount = $amount;
        $this->date = $date;
        $this->status = $status;
        $this->pdf_url = $pdf_url;
    }
}
