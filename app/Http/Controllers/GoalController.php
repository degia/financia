<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function __construct(
        protected GoalService $goalService
    ) {}

    public function index(Request $request): View
    {
        $goals = $request->user()->goals()->orderBy('is_achieved')->orderBy('target_date')->get();
        return view('goals.index', compact('goals'));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $this->goalService->createGoal([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'target_date' => $request->target_date,
            'color' => $request->color ?? '#6366F1',
        ]);

        return redirect()->route('goals.index')->with('success', 'Goal created successfully.');
    }

    public function edit(Goal $goal): View
    {
        $this->authorize('update', $goal);
        return view('goals.edit', compact('goal'));
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);
        $this->goalService->updateGoal($goal, $request->validated());
        return redirect()->route('goals.index')->with('success', 'Goal updated successfully.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);
        $this->goalService->deleteGoal($goal);
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully.');
    }

    public function contribute(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);
        $request->validate(['amount' => ['required', 'numeric', 'min:0']]);
        $this->goalService->addContribution($goal, $request->amount);
        return redirect()->route('goals.index')->with('success', 'Contribution added.');
    }
}
