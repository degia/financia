<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanPayment>
 */
class LoanPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'account_id' => Account::factory(),
            'transaction_id' => Transaction::factory(),
            'amount' => fake()->randomFloat(2, 50000, 5000000),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }
}
