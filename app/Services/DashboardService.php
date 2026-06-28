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
            ->whereNull('transfer_id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $netSavings = $monthlyIncome - $monthlyExpense;

        return compact('totalBalance', 'monthlyIncome', 'monthlyExpense', 'netSavings', 'accounts');
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
                ->whereNull('transfer_id')
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
            ->whereNull('transfer_id')
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
                ->whereNull('transfer_id')
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
}
