<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Food & Drinks', 'Transportation', 'Shopping', 'Entertainment',
                'Utilities', 'Salary', 'Freelance', 'Investment',
            ]),
            'type' => fake()->randomElement(['income', 'expense']),
            'icon' => fake()->randomElement(['utensils', 'car', 'cart-shopping', 'film', 'bolt', 'sack-dollar', 'laptop-code', 'chart-line']),
            'color' => fake()->hexColor(),
            'is_system' => false,
        ];
    }
}
