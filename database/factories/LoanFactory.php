<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100000, 50000000);

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['Home Renovation Loan', 'Business Capital', 'Personal Loan', 'Car Loan']),
            'type' => fake()->randomElement(['borrow', 'lend']),
            'lender_name' => fake()->company(),
            'amount' => $amount,
            'interest_rate' => fake()->randomFloat(2, 0, 15),
            'paid_amount' => 0,
            'remaining_amount' => $amount,
            'start_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'due_date' => fake()->dateTimeBetween('+6 months', '+5 years')->format('Y-m-d'),
            'notes' => fake()->optional(0.3)->sentence(),
            'status' => fake()->randomElement(['active', 'completed', 'defaulted']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_amount' => $attributes['amount'],
            'remaining_amount' => 0,
            'status' => 'completed',
        ]);
    }

    public function borrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'borrow',
        ]);
    }

    public function lend(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'lend',
        ]);
    }
}
