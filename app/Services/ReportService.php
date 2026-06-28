<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getMonthlyReport(User $user, int $year): array
    {
        $months = range(1, 12);
        $data = [];

        foreach ($months as $month) {
            $income = (float) $user->transactions()
                ->where('type', 'income')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $expense = (float) $user->transactions()
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $data[] = [
                'month' => $month,
                'month_name' => now()->month($month)->format('F'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        }

        return $data;
    }

    public function getCategoryReport(User $user, string $startDate, string $endDate): array
    {
        $expenses = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $incomes = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $totalIncome = $incomes->sum('total');
        $totalExpense = $expenses->sum('total');

        return compact('expenses', 'incomes', 'totalIncome', 'totalExpense');
    }

    public function getTransactionsReport(User $user, array $filters): array
    {
        $query = $user->transactions()
            ->with(['account', 'category'])
            ->orderBy('date', 'desc');

        if (!empty($filters['start_date'])) {
            $query->where('date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('date', '<=', $filters['end_date']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        return compact('transactions', 'totalIncome', 'totalExpense');
    }
}
