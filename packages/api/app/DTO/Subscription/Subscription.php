<?php

namespace App\DTO\Subscription;

final class Subscription
{
    public string $plan_name;

    public int $plan_price;

    public ?SubscriptionPaymentMethod $payment_method;

    public string $billing_email;

    public function __construct(string $plan_name, int $plan_price, ?SubscriptionPaymentMethod $payment_method, string $billing_email)
    {
        $this->plan_name = $plan_name;
        $this->plan_price = $plan_price;
        $this->payment_method = $payment_method;
        $this->billing_email = $billing_email;
    }
}
