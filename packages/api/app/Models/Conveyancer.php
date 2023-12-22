<?php

namespace App\Models;

use App\DTO\Subscription\Invoice;
use App\DTO\Subscription\Subscription;
use App\DTO\Subscription\SubscriptionPaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stripe\StripeClient;

class Conveyancer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'company_number',
        'sra_clc_number',
        'client_care_letter',
        'client_care_letter_sale',
        'client_care_letter_purchase',
        'client_care_letter_remortgage',
        'terms_and_conditions',
        'letter_header',
        'letter_footer',
        'trading_name',
        'vat_number',
        'website',
        'location',
        'telephone_number',
        'email_address',
    ];

    /**
     * Get the logo image
     */
    public function getLogoImageAttribute(): ?Media
    {
        return $this->getFirstMedia('logo_image');
    }

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo_image')->singleFile();
    }

    /**
     * Team members relationship
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Properties relationship
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Address relationship
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function getSubscription()
    {
        /** @var StripeClient */
        $stripe = app(StripeClient::class);

        $customer = $stripe->customers->retrieve(
            $this->stripe_customer_id,
            ['expand' => ['subscriptions', 'invoice_settings.default_payment_method']]
        );

        $product = $stripe->products->retrieve(
            $customer->subscriptions->data[0]->items->data[0]->price->product,
            []
        );

        $paymentMethod = $customer->invoice_settings->default_payment_method
            ? new SubscriptionPaymentMethod(
                type: $customer->invoice_settings->default_payment_method->type,
                brand: $customer->invoice_settings->default_payment_method->card->brand,
                exp_month: $customer->invoice_settings->default_payment_method->card->exp_month,
                exp_year: $customer->invoice_settings->default_payment_method->card->exp_year,
                last4: $customer->invoice_settings->default_payment_method->card->last4 ?? $customer->invoice_settings->default_payment_method->bacs_debit->last4,
                sort_code: $customer->invoice_settings->default_payment_method->bacs_debit?->sort_code,
            )
            : null;

        return new Subscription(
            plan_name: $product->name,
            plan_price: $customer->subscriptions->data[0]->items->data[0]->price->unit_amount,
            payment_method: $paymentMethod,
            billing_email: $customer->email,
        );
    }

    public function getInvoices()
    {
        /** @var StripeClient */
        $stripe = app(StripeClient::class);

        $invoices = [];

        $upcomingInvoice = $stripe->invoices->upcoming([
            'customer' => $this->stripe_customer_id,
        ]);

        if ($upcomingInvoice) {
            $invoices[] = $upcomingInvoice;
        }

        $invoices = [
            ...$invoices,
            ...$stripe->invoices->all([
                'customer' => $this->stripe_customer_id,
            ])->data,
        ];

        $productIds = array_map(function ($invoice) {
            return $invoice->lines->data[0]->price->product;
        }, $invoices);

        $products = collect($stripe->products->all([
            'ids' => $productIds,
        ])->data);

        return array_map(function ($invoice) use ($products) {
            return new Invoice(
                plan_name: $products->first(fn ($p) => $p->id === $invoice->lines->data[0]->price->product)->name,
                number: $invoice->number,
                amount: $invoice->status === 'draft'
                    ? $invoice->amount_due
                    : $invoice->amount_paid,
                date: Carbon::createFromTimestamp($invoice->period_end),
                status: $invoice->status,
                pdf_url: optional($invoice)->invoice_pdf,
            );
        }, $invoices);
    }
}
