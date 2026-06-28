<?php

namespace App\Services;

use App\Models\Account;
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
                // Same account, apply new effect
                $type = $data['type'] ?? $transaction->type;
                $amount = $data['amount'] ?? $transaction->amount;

                if ($type === 'income') {
                    $account->increment('current_balance', $amount);
                } else {
                    $account->decrement('current_balance', $amount);
                }
            }

            $transaction->update($data);
            return $transaction->fresh();
        });
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
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
}
