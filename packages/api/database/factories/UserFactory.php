<?php

namespace Database\Factories;

use App\Enums\UserJobRole;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'role' => UserRole::getRandomValue(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'title' => fake()->title(),
            'suffix' => fake('en_US')->suffix(),
            'job_role' => UserJobRole::getRandomValue(),
            'occupation' => fake('en_US')->jobTitle(),
            'job_bio' => fake()->paragraph(),
            'phone' => fake()->mobileNumber(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'invite_code_sent_at' => null,
            'sra_clc_number' => fake()->randomNumber(8),
        ];
    }

    /**
     * Create an admin user
     *
     * @return static
     */
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin,
        ]);
    }
}
