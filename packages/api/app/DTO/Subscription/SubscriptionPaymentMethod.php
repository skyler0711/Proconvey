<?php

namespace App\DTO\Subscription;

final class SubscriptionPaymentMethod
{
    public string $type;

    public string $brand;

    public int $exp_month;

    public int $exp_year;

    public string $last4;

    public ?string $sort_code;

    public function __construct(string $type, string $brand, int $exp_month, int $exp_year, string $last4, ?string $sort_code)
    {
        $this->type = $type;
        $this->brand = $brand;
        $this->exp_month = $exp_month;
        $this->exp_year = $exp_year;
        $this->last4 = $last4;
        $this->sort_code = $sort_code;
    }
}
