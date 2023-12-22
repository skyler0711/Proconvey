<?php

namespace App\Services\OnboardingLettersService;

use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Mustache_Engine;

final class OnboardingLettersService
{
    public function getHtml(string $content, bool $preview = false, ?User $user = null, ?Property $property = null): string
    {
        $data = $preview ? $this->getPreviewData() : $this->getData($user, $property);

        /** @var Mustache_Engine */
        $engine = app()->make(Mustache_Engine::class);

        return $engine->render($content, $data);
    }

    protected function getData(User $user, Property $property): array
    {
        return [
            'user' => [
                'full_name' => $user->full_name,
                'title' => $user->title,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'suffix' => $user->suffix,
                'email' => $user->email,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified_at,
            ],
            'conveyancer' => [
                'name' => $property->conveyancer->name,
                'telephone_number' => $property->conveyancer->telephone_number,
                'email_address' => $property->conveyancer->email_address,
                'company_number' => $property->conveyancer->company_number,
                'sra_clc_number' => $property->conveyancer->sra_clc_number,
                'website' => $property->conveyancer->website,
                'location' => $property->conveyancer->location,
                'trading_name' => $property->conveyancer->trading_name,
                'vat_number' => $property->conveyancer->vat_number,
                'type' => $property->conveyancer->type,
            ],
            'property' => [
                'case_reference' => $property->case_reference,
                'type' => $property->type,
                'sale_price' => '£'.number_format($property->sale_price),
                'conveyancing_fee' => '£'.number_format($property->conveyancing_fee),
                'fee_earner' => $property->feeEarner?->full_name,
                'address' => [
                    'line_1' => $property->address->line_1,
                    'line_2' => $property->address->line_2,
                    'city' => $property->address->city,
                    'postcode' => $property->address->postcode,
                    'single_line' => $property->address->single_line,
                ],
                'payment_on_account_amount' => $property->payment_on_account_amount,
            ],
        ];
    }

    protected function getPreviewData(): array
    {
        /** @var Conveyancer */
        $conveyancer = auth()->user()?->conveyancer ?? Conveyancer::factory()->make();

        return [
            'user' => [
                'full_name' => 'Mr John Doe',
                'title' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'suffix' => '',
                'email' => 'john@example.com',
                'phone' => '01234567890',
            ],
            'conveyancer' => [
                'name' => $conveyancer->name,
                'company_number' => $conveyancer->company_number,
                'sra_clc_number' => $conveyancer->sra_clc_number,
            ],
            'property' => [
                'case_reference' => 'TEST-REF',
                'sale_price' => '£700,000',
                'conveyancing_fee' => '£150',
                'fee_earner' => 'John Doe',
                'address' => [
                    'line_1' => '1 Example Street',
                    'line_2' => null,
                    'city' => 'Example City',
                    'postcode' => 'AB1 2CD',
                    'single_line' => '1 Example Street, Example City, AB1 2CD',
                ],
            ],
        ];
    }
}
