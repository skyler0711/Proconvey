<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'addressable_id' => fake()->randomElement(User::where('role', UserRole::Client)->pluck('id')),
            'line_1' => fake()->streetAddress(),
            'line_2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'postcode' => fake()->postcode(),
            'uprn' => fake()->randomNumber(8),
        ];
    }
}
