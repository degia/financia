<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'institution_id' => null,
            'name' => fake()->randomElement(['Checking', 'Savings', 'Cash Wallet', 'Credit Card', 'Emergency Fund']),
            'type' => fake()->randomElement(['cash', 'bank', 'ewallet', 'credit_card', 'savings']),
            'category' => fake()->randomElement(['real', 'savings', 'subscriptions']),
            'initial_balance' => fake()->randomFloat(2, 0, 10000),
            'current_balance' => fn (array $attrs) => $attrs['initial_balance'] + fake()->randomFloat(2, -5000, 5000),
            'currency' => 'USD',
            'icon' => fake()->randomElement(['wallet', 'building-columns', 'credit-card', 'piggy-bank']),
            'color' => fake()->hexColor(),
        ];
    }
}
