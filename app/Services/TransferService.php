<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function transfer(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
            $fromAccount = Account::where('id', $data['from_account_id'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $toAccount = Account::where('id', $data['to_account_id'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($fromAccount->id === $toAccount->id) {
                abort(422, 'Cannot transfer to the same account.');
            }

            $transferExpense = Category::where('user_id', $user->id)
                ->where('name', 'Transfer')
                ->where('type', 'expense')
                ->firstOrFail();

            $transferIncome = Category::where('user_id', $user->id)
                ->where('name', 'Transfer')
                ->where('type', 'income')
                ->firstOrFail();

            $date = $data['date'] ?? now()->format('Y-m-d');
            $description = $data['description'] ?? 'Transfer';

            $outgoing = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $data['from_account_id'],
                'category_id' => $transferExpense->id,
                'amount' => $data['amount'],
                'type' => 'expense',
                'description' => 'Transfer to ' . $toAccount->name . ($description !== 'Transfer' ? ': ' . $description : ''),
                'date' => $date,
            ]);

            $incoming = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $data['to_account_id'],
                'category_id' => $transferIncome->id,
                'amount' => $data['amount'],
                'type' => 'income',
                'description' => 'Transfer from ' . $fromAccount->name . ($description !== 'Transfer' ? ': ' . $description : ''),
                'date' => $date,
            ]);

            $outgoing->update(['transfer_id' => $incoming->id]);
            $incoming->update(['transfer_id' => $outgoing->id]);

            $fromAccount->decrement('current_balance', $data['amount']);
            $toAccount->increment('current_balance', $data['amount']);

            return [$outgoing, $incoming];
        });
    }

    public function deleteTransfer(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $paired = $transaction->transfer;

            $account = Account::where('id', $transaction->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($transaction->type === 'expense') {
                $account->increment('current_balance', $transaction->amount);
            } else {
                $account->decrement('current_balance', $transaction->amount);
            }

            $transaction->delete();

            if ($paired) {
                $pairedAccount = Account::where('id', $paired->account_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($paired->type === 'expense') {
                    $pairedAccount->increment('current_balance', $paired->amount);
                } else {
                    $pairedAccount->decrement('current_balance', $paired->amount);
                }

                $paired->delete();
            }
        });
    }
}
