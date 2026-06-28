<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function createAccount(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            $data['current_balance'] = $data['initial_balance'] ?? 0;
            return Account::create($data);
        });
    }

    public function updateAccount(Account $account, array $data): Account
    {
        return DB::transaction(function () use ($account, $data) {
            $account->update($data);
            return $account->fresh();
        });
    }

    public function deleteAccount(Account $account): void
    {
        DB::transaction(function () use ($account) {
            $account->transactions()->delete();
            $account->delete();
        });
    }

    public function recalculateBalance(Account $account): void
    {
        $balance = $account->initial_balance;
        $balance += (float) $account->transactions()
            ->where('type', 'income')
            ->sum('amount');
        $balance -= (float) $account->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $account->update(['current_balance' => $balance]);
    }
}
