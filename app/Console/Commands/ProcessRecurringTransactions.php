<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'transactions:process-recurring
        {--date= : Base date for generating next occurrences (default: today)}
        {--dry-run : Show what would be created without inserting}';

    protected $description = 'Generate new transactions from recurring templates';

    public function handle(TransactionService $transactionService): int
    {
        $baseDate = $this->option('date') ? Carbon::parse($this->option('date')) : now()->startOfDay();
        $dryRun = $this->option('dry-run');
        $created = 0;
        $skipped = 0;

        $templates = Transaction::where('is_recurring', true)
            ->whereNotNull('recurring_interval')
            ->with(['account', 'category', 'subCategory'])
            ->get();

        foreach ($templates as $template) {
            $lastDate = $this->getLastGeneratedDate($template);
            $nextDate = $this->getNextOccurrence($lastDate, $template->recurring_interval);

            if (!$nextDate || $nextDate->gt($baseDate)) {
                $skipped++;
                continue;
            }

            $descSuffix = ' (recurring #' . $template->id . ')';
            $description = $template->description
                ? $template->description . $descSuffix
                : ($template->category?->name ?? 'Transaction') . $descSuffix;

            $data = [
                'user_id' => $template->user_id,
                'account_id' => $template->account_id,
                'category_id' => $template->category_id,
                'sub_category_id' => $template->sub_category_id,
                'amount' => $template->amount,
                'type' => $template->type,
                'description' => $description,
                'date' => $nextDate->format('Y-m-d'),
            ];

            if ($dryRun) {
                $this->line(sprintf('[DRY-RUN] Would create: %s %s on %s (%s)',
                    $template->type, number_format($template->amount, 2), $nextDate->format('Y-m-d'), $description));
            } else {
                $transactionService->createTransaction($data);
                $this->line(sprintf('Created: %s %s on %s (%s)',
                    $template->type, number_format($template->amount, 2), $nextDate->format('Y-m-d'), $description));
            }

            $created++;
        }

        $this->info(sprintf('Done. %d created, %d skipped.', $created, $skipped));
        return 0;
    }

    protected function getLastGeneratedDate(Transaction $template): Carbon
    {
        $descPattern = '%(recurring #' . $template->id . ')%';

        $last = Transaction::where('user_id', $template->user_id)
            ->where('account_id', $template->account_id)
            ->where('category_id', $template->category_id)
            ->where('amount', $template->amount)
            ->where('type', $template->type)
            ->where('description', 'like', $descPattern)
            ->where('id', '!=', $template->id)
            ->orderBy('date', 'desc')
            ->first();

        if ($last) {
            return $last->date instanceof Carbon ? $last->date->copy() : Carbon::parse($last->date);
        }

        return $template->date instanceof Carbon ? $template->date->copy() : Carbon::parse($template->date);
    }

    protected function getNextOccurrence(Carbon $lastDate, string $interval): Carbon
    {
        return match ($interval) {
            'daily' => $lastDate->copy()->addDay(),
            'weekly' => $lastDate->copy()->addWeek(),
            'monthly' => $lastDate->copy()->addMonth(),
            'yearly' => $lastDate->copy()->addYear(),
            default => $lastDate->copy()->addDay(),
        };
    }
}
