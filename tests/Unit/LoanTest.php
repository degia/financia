<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\LoanFactory;
use Database\Factories\LoanPaymentFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Loan())->getFillable();

        $this->assertEquals([
            'user_id',
            'account_id',
            'name',
            'type',
            'lender_name',
            'amount',
            'interest_rate',
            'paid_amount',
            'remaining_amount',
            'start_date',
            'due_date',
            'notes',
            'status',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Loan())->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('decimal:2', $casts['interest_rate']);
        $this->assertEquals('decimal:2', $casts['paid_amount']);
        $this->assertEquals('decimal:2', $casts['remaining_amount']);
        $this->assertEquals('date', $casts['start_date']);
        $this->assertEquals('date', $casts['due_date']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $loan = LoanFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $loan->user());
        $this->assertInstanceOf(User::class, $loan->user);
        $this->assertTrue($loan->user->is($user));
    }

    public function test_account_relationship()
    {
        $account = AccountFactory::new()->create();
        $loan = LoanFactory::new()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(BelongsTo::class, $loan->account());
        $this->assertInstanceOf(Account::class, $loan->account);
        $this->assertTrue($loan->account->is($account));
    }

    public function test_payments_relationship()
    {
        $loan = LoanFactory::new()->create();
        $payment = LoanPaymentFactory::new()->create(['loan_id' => $loan->id]);

        $this->assertInstanceOf(HasMany::class, $loan->payments());
        $this->assertInstanceOf(LoanPayment::class, $loan->payments->first());
        $this->assertTrue($loan->payments->first()->is($payment));
    }
}
