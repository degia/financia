<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Console\Command;

class ReconcileAccounts extends Command
{
    protected $signature = 'accounts:reconcile {--dry-run : Show discrepancies without updating}';

    protected $description = 'Recalculate all account balances based on transactions';

    public function handle(AccountService $accountService): int
    {
        $accounts = Account::with('user')->get();
        $fixed = 0;
        $errors = 0;

        foreach ($accounts as $account) {
            $calculated = (float) $account->initial_balance;
            $calculated += (float) $account->transactions()
                ->where('type', 'income')
                ->sum('amount');
            $calculated -= (float) $account->transactions()
                ->where('type', 'expense')
                ->sum('amount');

            $current = (float) $account->current_balance;
            $diff = abs($calculated - $current);

            if ($diff > 0.01) {
                $userName = $account->user?->name ?? 'Unknown';
                $this->warn(sprintf(
                    '[%s] %s (%s): current=%.2f calculated=%.2f diff=%.2f',
                    $userName,
                    $account->name,
                    $account->id,
                    $current,
                    $calculated,
                    $calculated - $current
                ));

                if (!$this->option('dry-run')) {
                    $accountService->recalculateBalance($account);
                    $fixed++;
                }
            }
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. Use --no-dry-run to fix.');
        } else {
            $this->info("Done. {$fixed} accounts reconciled.");
        }

        return 0;
    }
}
