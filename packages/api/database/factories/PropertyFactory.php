<?php

namespace Database\Factories;

use App\Enums\PropertyType;
use App\Models\Conveyancer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $randomConveyancer = Conveyancer::query()->inRandomOrder()->first();
        $lettersRequired = fake()->boolean();
        $paymentRequired = fake()->boolean();

        return [
            'case_reference' => fake()->numerify('REF-######'),
            'letters_required' => $lettersRequired,
            'conveyancer_id' => $randomConveyancer->id,
            'id_check_required' => fake()->boolean(),
            'sof_check_required' => fake()->boolean(),
            'type' => PropertyType::getRandomValue(),
            'sale_price' => $lettersRequired ? fake()->numberBetween(100000, 10000000) : null,
            'conveyancing_fee' => $lettersRequired ? fake()->numberBetween(1000, 10000) : null,
            'fee_earner_id' => $lettersRequired
                ? User::query()
                    ->where('conveyancer_id', $randomConveyancer->id)
                    ->inRandomOrder()
                    ->first()
                    ?->id
                : null,
            'payment_required' => $paymentRequired,
            'payment_amount' => $paymentRequired ? fake()->numberBetween(100000, 10000000) : null,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
