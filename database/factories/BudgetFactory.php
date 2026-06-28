<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->year(),
            'notify_at' => fake()->randomElement([50.00, 75.00, 80.00, 90.00]),
        ];
    }
}
