<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummary(User $user, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $accounts = $user->accounts;

        $totalBalance = $accounts->sum('current_balance');

        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereNull('transfer_id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $netSavings = $monthlyIncome - $monthlyExpense;

        $realBalance = (float) $accounts->where('category', '!=', 'savings')->sum('current_balance');
        $savingsBalance = (float) $accounts->where('category', 'savings')->sum('current_balance');

        return compact('totalBalance', 'realBalance', 'savingsBalance', 'monthlyIncome', 'monthlyExpense', 'netSavings', 'accounts');
    }

    public function getMonthlyChart(User $user, int $year): array
    {
        $months = range(1, 12);
        $income = [];
        $expense = [];

        foreach ($months as $m) {
            $income[] = (float) $user->transactions()
                ->where('type', 'income')
                ->whereNull('transfer_id')
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');

            $expense[] = (float) $user->transactions()
                ->where('type', 'expense')
                ->where(function ($q) {
                    $q->whereNull('transfer_id')->orWhere('is_savings', true);
                })
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'income' => $income,
            'expense' => $expense,
        ];
    }

    public function getCategoryBreakdown(User $user, int $month, int $year): array
    {
        $expenses = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($expenses as $expense) {
            $labels[] = $expense->category->name ?? 'Unknown';
            $data[] = (float) $expense->total;
            $colors[] = $expense->category->color ?? '#6B7280';
        }

        return compact('labels', 'data', 'colors');
    }

    public function getBudgetsProgress(User $user, int $month, int $year): array
    {
        $budgets = $user->budgets()->with('category')->where('month', $month)->where('year', $year)->get();
        $progress = [];

        foreach ($budgets as $budget) {
            $spent = (float) $user->transactions()
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->where(function ($q) {
                    $q->whereNull('transfer_id')->orWhere('is_savings', true);
                })
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $percentage = $budget->amount > 0 ? min(100, round(($spent / $budget->amount) * 100, 1)) : 0;

            $progress[] = [
                'category' => $budget->category->name ?? 'Unknown',
                'budgeted' => (float) $budget->amount,
                'spent' => $spent,
                'percentage' => $percentage,
                'color' => $budget->category->color ?? '#6366F1',
            ];
        }

        return $progress;
    }

    public function getDailyChart(User $user, int $month, int $year): array
    {
        $daysInMonth = now()->setYear($year)->setMonth($month)->daysInMonth;

        $transactions = $user->transactions()
            ->select(DB::raw('DAYOFMONTH(date) as day'), 'type', DB::raw('SUM(amount) as total'))
            ->whereNull('transfer_id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get();

        $income = array_fill(1, $daysInMonth, 0);
        $expense = array_fill(1, $daysInMonth, 0);

        foreach ($transactions as $t) {
            $day = (int) $t->day;
            if ($t->type === 'income') {
                $income[$day] += (float) $t->total;
            } else {
                $expense[$day] += (float) $t->total;
            }
        }

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $daily = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = (string) $d;
            $incomeData[] = $income[$d];
            $expenseData[] = $expense[$d];
            $daily[] = [
                'day' => $d,
                'income' => $income[$d],
                'expense' => $expense[$d],
                'net' => $income[$d] - $expense[$d],
            ];
        }

        return compact('labels', 'incomeData', 'expenseData', 'daily');
    }

    public function getSavingsSummary(User $user, int $month, int $year): array
    {
        $totalSavings = (float) $user->transactions()
            ->where('type', 'expense')
            ->where('is_savings', true)
            ->sum('amount');

        $monthlySavings = (float) $user->transactions()
            ->where('type', 'expense')
            ->where('is_savings', true)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $savingsCategoryTotal = (float) $user->transactions()
            ->where('type', 'expense')
            ->whereHas('category', fn($q) => $q->where('name', 'Savings'))
            ->whereNull('transfer_id')
            ->sum('amount');

        return [
            'totalSaved' => $totalSavings + $savingsCategoryTotal,
            'monthlySavings' => $monthlySavings,
            'savingsCount' => $user->transactions()
                ->where('type', 'expense')
                ->where('is_savings', true)
                ->count(),
        ];
    }
}
