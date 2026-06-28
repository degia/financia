<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Transaction;
use Database\Factories\AccountFactory;
use Database\Factories\LoanFactory;
use Database\Factories\LoanPaymentFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new LoanPayment())->getFillable();

        $this->assertEquals([
            'loan_id',
            'account_id',
            'transaction_id',
            'amount',
            'payment_date',
            'notes',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new LoanPayment())->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('date', $casts['payment_date']);
    }

    public function test_loan_relationship()
    {
        $loan = LoanFactory::new()->create();
        $payment = LoanPaymentFactory::new()->create(['loan_id' => $loan->id]);

        $this->assertInstanceOf(BelongsTo::class, $payment->loan());
        $this->assertInstanceOf(Loan::class, $payment->loan);
        $this->assertTrue($payment->loan->is($loan));
    }

    public function test_account_relationship()
    {
        $account = AccountFactory::new()->create();
        $payment = LoanPaymentFactory::new()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(BelongsTo::class, $payment->account());
        $this->assertInstanceOf(Account::class, $payment->account);
        $this->assertTrue($payment->account->is($account));
    }

    public function test_transaction_relationship()
    {
        $transaction = TransactionFactory::new()->create();
        $payment = LoanPaymentFactory::new()->create(['transaction_id' => $transaction->id]);

        $this->assertInstanceOf(BelongsTo::class, $payment->transaction());
        $this->assertInstanceOf(Transaction::class, $payment->transaction);
        $this->assertTrue($payment->transaction->is($transaction));
    }
}
