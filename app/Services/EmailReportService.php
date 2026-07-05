<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmailReportService
{
    public function gatherData(User $user, string $date, array $sections = []): array
    {
        $defaultSections = ['income', 'expense', 'net', 'categories', 'accounts', 'transactions', 'budgets'];
        $sections = !empty($sections) ? $sections : $defaultSections;

        $dateObj = Carbon::parse($date);
        $data = [];

        if (in_array('income', $sections)) {
            $data['income'] = (float) $user->transactions()
                ->where('type', 'income')
                ->whereNull('transfer_id')
                ->whereDate('date', $date)
                ->sum('amount');
        }

        if (in_array('expense', $sections)) {
            $data['expense'] = (float) $user->transactions()
                ->where('type', 'expense')
                ->where(function ($q) {
                    $q->whereNull('transfer_id')->orWhere('is_savings', true);
                })
                ->whereDate('date', $date)
                ->sum('amount');
        }

        $data['net'] = ($data['income'] ?? 0) - ($data['expense'] ?? 0);

        if (in_array('categories', $sections)) {
            $categories = collect();

            foreach (['income', 'expense'] as $type) {
                $rows = $user->transactions()
                    ->select('category_id', DB::raw('SUM(amount) as total'))
                    ->where('type', $type)
                    ->whereDate('date', $date);

                if ($type === 'expense') {
                    $rows->where(function ($q) {
                        $q->whereNull('transfer_id')->orWhere('is_savings', true);
                    });
                } else {
                    $rows->whereNull('transfer_id');
                }

                $rows = $rows->groupBy('category_id')
                    ->with('category')
                    ->orderByDesc('total')
                    ->get()
                    ->map(fn($r) => [
                        'name' => $r->category->name ?? 'Unknown',
                        'total' => (float) $r->total,
                        'type' => $type,
                    ]);

                $categories = $categories->concat($rows);
            }

            $data['categories'] = $categories->toArray();
        }

        if (in_array('accounts', $sections)) {
            $accounts = $user->accounts()
                ->where('current_balance', '>', 0)
                ->orderBy('name')
                ->get(['name', 'current_balance']);

            $data['accounts'] = $accounts->map(fn($a) => [
                'name' => $a->name,
                'balance' => (float) $a->current_balance,
            ])->toArray();
        }

        if (in_array('transactions', $sections)) {
            $transactions = $user->transactions()
                ->with(['category', 'account'])
                ->whereDate('date', $date)
                ->whereNull('transfer_id')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $data['transactions'] = $transactions->map(fn($t) => [
                'description' => $t->description,
                'category' => $t->category->name ?? '-',
                'account' => $t->account->name ?? '-',
                'amount' => (float) $t->amount,
                'type' => $t->type,
            ])->toArray();
        }

        if (in_array('budgets', $sections)) {
            $month = $dateObj->month;
            $year = $dateObj->year;
            $budgets = $user->budgets()->with('category')
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $data['budgets'] = $budgets->map(function ($b) use ($user, $month, $year) {
                $spent = (float) $user->transactions()
                    ->where('category_id', $b->category_id)
                    ->where('type', 'expense')
                    ->where(function ($q) {
                        $q->whereNull('transfer_id')->orWhere('is_savings', true);
                    })
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('amount');

                return [
                    'category' => $b->category->name ?? 'Unknown',
                    'budgeted' => (float) $b->amount,
                    'spent' => $spent,
                    'percentage' => $b->amount > 0 ? min(999, round(($spent / $b->amount) * 100, 1)) : 0,
                ];
            })->toArray();

            $data['budgets_month'] = $dateObj->format('F Y');
        }

        return $data;
    }
}
