<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\User;

class BudgetService
{
    public function createBudget(array $data): Budget
    {
        return Budget::create($data);
    }

    public function updateBudget(Budget $budget, array $data): Budget
    {
        $budget->update($data);
        return $budget->fresh();
    }

    public function deleteBudget(Budget $budget): void
    {
        $budget->delete();
    }

    public function calculateProgress(User $user, int $month, int $year): array
    {
        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $progress = [];

        foreach ($budgets as $budget) {
            $spent = (float) $user->transactions()
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $percentage = $budget->amount > 0
                ? round(($spent / $budget->amount) * 100, 1)
                : 0;

            $progress[] = [
                'budget' => $budget,
                'spent' => $spent,
                'percentage' => $percentage,
                'status' => $percentage <= 50 ? 'good' : ($percentage <= 80 ? 'warning' : ($percentage <= 100 ? 'danger' : 'exceeded')),
            ];
        }

        return $progress;
    }
}
