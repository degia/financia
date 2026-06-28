<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
            'sub_category_id' => null,
            'transfer_id' => null,
            'is_savings' => false,
            'amount' => fake()->randomFloat(2, 10, 5000),
            'type' => $type,
            'description' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'is_recurring' => fake()->boolean(10),
            'recurring_interval' => null,
        ];
    }

    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurring_interval' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
        ]);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
        ]);
    }

    public function savings(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_savings' => true,
            'type' => 'expense',
        ]);
    }
}
