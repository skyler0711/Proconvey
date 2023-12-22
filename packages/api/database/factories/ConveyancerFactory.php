<?php

namespace Database\Factories;

use App\Enums\ConveyancerType;
use App\Enums\IDProviders;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ConveyancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'stripe_customer_id' => 'test_123',
            'type' => ConveyancerType::getRandomValue(),
            'id_provider' => IDProviders::getRandomValue(),
            'name' => fake()->company(),
            'company_number' => fake()->randomNumber(8),
            'sra_clc_number' => fake()->randomNumber(6),
            'trading_name' => fake()->company(),
            'vat_number' => fake()->randomNumber(6),
            'website' => fake()->url(),
            'location' => fake()->city(),
            'telephone_number' => fake()->phoneNumber(),
            'email_address' => fake()->companyEmail(),
        ];
    }
}
