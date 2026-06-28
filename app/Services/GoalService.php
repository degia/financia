<?php

namespace App\Services;

use App\Models\Goal;

class GoalService
{
    public function createGoal(array $data): Goal
    {
        return Goal::create($data);
    }

    public function updateGoal(Goal $goal, array $data): Goal
    {
        $goal->update($data);
        return $goal->fresh();
    }

    public function deleteGoal(Goal $goal): void
    {
        $goal->delete();
    }

    public function addContribution(Goal $goal, float $amount): Goal
    {
        $goal->increment('current_amount', $amount);

        if ($goal->current_amount >= $goal->target_amount) {
            $goal->update(['is_achieved' => true]);
        }

        return $goal->fresh();
    }
}
