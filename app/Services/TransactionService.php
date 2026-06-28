<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            $account = Account::where('id', $data['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($data['type'] === 'income') {
                $account->increment('current_balance', $data['amount']);
            } else {
                $account->decrement('current_balance', $data['amount']);
            }

            if (!empty($data['loan_id'])) {
                $this->applyLoanPayment($transaction, $data);
            }

            return $transaction;
        });
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $account = Account::where('id', $transaction->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Reverse old transaction effect
            if ($transaction->type === 'income') {
                $account->decrement('current_balance', $transaction->amount);
            } else {
                $account->increment('current_balance', $transaction->amount);
            }

            // If account changed, apply reverse to old account and update new one
            if (isset($data['account_id']) && $data['account_id'] !== $transaction->account_id) {
                $newAccount = Account::where('id', $data['account_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($data['type'] ?? $transaction->type === 'income') {
                    $newAccount->increment('current_balance', $data['amount'] ?? $transaction->amount);
                } else {
                    $newAccount->decrement('current_balance', $data['amount'] ?? $transaction->amount);
                }
            } else {
                $type = $data['type'] ?? $transaction->type;
                $amount = $data['amount'] ?? $transaction->amount;

                if ($type === 'income') {
                    $account->increment('current_balance', $amount);
                } else {
                    $account->decrement('current_balance', $amount);
                }
            }

            // Handle loan payment changes
            $this->syncLoanPayment($transaction, $data);

            $transaction->update($data);
            return $transaction->fresh();
        });
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $loanPayment = $transaction->loanPayment;
            if ($loanPayment) {
                $loan = Loan::where('id', $loanPayment->loan_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $loan->paid_amount -= $loanPayment->amount;
                $loan->remaining_amount += $loanPayment->amount;
                $loan->status = 'active';
                $loan->save();
            }

            $account = Account::where('id', $transaction->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($transaction->type === 'income') {
                $account->decrement('current_balance', $transaction->amount);
            } else {
                $account->increment('current_balance', $transaction->amount);
            }

            $transaction->delete();
        });
    }

    protected function applyLoanPayment(Transaction $transaction, array $data): void
    {
        $loan = Loan::where('id', $data['loan_id'])
            ->where('user_id', $transaction->user_id)
            ->lockForUpdate()
            ->firstOrFail();

        LoanPayment::create([
            'loan_id' => $loan->id,
            'account_id' => $data['account_id'],
            'transaction_id' => $transaction->id,
            'amount' => $data['amount'],
            'payment_date' => $data['date'] ?? now(),
        ]);

        $loan->paid_amount += $data['amount'];
        $loan->remaining_amount -= $data['amount'];

        if ($loan->remaining_amount <= 0) {
            $loan->status = 'completed';
            $loan->remaining_amount = 0;
        }

        $loan->save();
    }

    protected function syncLoanPayment(Transaction $transaction, array $data): void
    {
        $oldLoanId = $transaction->loan_id;
        $newLoanId = $data['loan_id'] ?? null;

        // No change
        if (!$oldLoanId && !$newLoanId) return;

        // Same loan: update existing payment amount if changed
        if ($oldLoanId && $oldLoanId == $newLoanId) {
            $loanPayment = $transaction->loanPayment;
            if (!$loanPayment) return;

            $loan = Loan::where('id', $oldLoanId)->lockForUpdate()->firstOrFail();

            $diff = ($data['amount'] ?? $transaction->amount) - $transaction->amount;
            if ($diff != 0) {
                $loan->paid_amount += $diff;
                $loan->remaining_amount -= $diff;
                if ($loan->remaining_amount <= 0) {
                    $loan->status = 'completed';
                    $loan->remaining_amount = 0;
                }
                $loan->save();

                $loanPayment->update([
                    'amount' => $data['amount'] ?? $transaction->amount,
                    'account_id' => $data['account_id'] ?? $transaction->account_id,
                    'payment_date' => $data['date'] ?? $transaction->date,
                ]);
            }
            return;
        }

        // Loan changed: reverse old, apply new
        if ($oldLoanId) {
            $loanPayment = $transaction->loanPayment;
            if ($loanPayment) {
                $loan = Loan::where('id', $oldLoanId)->lockForUpdate()->firstOrFail();
                $loan->paid_amount -= $loanPayment->amount;
                $loan->remaining_amount += $loanPayment->amount;
                $loan->status = 'active';
                $loan->save();

                $loanPayment->delete();
            }
        }

        if ($newLoanId) {
            $this->applyLoanPayment($transaction, $data);
        }
    }
}
