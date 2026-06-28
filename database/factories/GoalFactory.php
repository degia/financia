<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
{
    public function definition(): array
    {
        $targetAmount = fake()->randomFloat(2, 500, 100000);

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Emergency Fund', 'New Laptop', 'Vacation Trip', 'Down Payment',
                'Car Fund', 'Wedding Savings', 'Education Fund',
            ]),
            'target_amount' => $targetAmount,
            'current_amount' => fake()->randomFloat(2, 0, $targetAmount),
            'target_date' => fake()->dateTimeBetween('+1 month', '+3 years')->format('Y-m-d'),
            'icon' => fake()->randomElement(['umbrella', 'laptop', 'plane', 'house', 'car', 'ring', 'graduation-cap']),
            'color' => fake()->hexColor(),
            'is_achieved' => false,
        ];
    }

    public function achieved(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_amount' => $attributes['target_amount'],
            'is_achieved' => true,
        ]);
    }
}
