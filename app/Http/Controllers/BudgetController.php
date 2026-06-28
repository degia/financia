<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    public function index(Request $request): View
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $budgets = $this->budgetService->calculateProgress($request->user(), $month, $year);
        return view('budgets.index', compact('budgets', 'month', 'year'));
    }

    public function create(Request $request): View
    {
        $usedCategoryIds = $request->user()->budgets()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->pluck('category_id');

        $categories = $request->user()->categories()
            ->where('type', 'expense')
            ->whereNotIn('id', $usedCategoryIds)
            ->orderBy('name')
            ->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $this->budgetService->createBudget([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'month' => $request->month,
            'year' => $request->year,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);
        $categories = $budget->user->categories()->where('type', 'expense')->orderBy('name')->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);
        $this->budgetService->updateBudget($budget, $request->validated());
        return redirect()->route('budgets.index', ['month' => $budget->month, 'year' => $budget->year])
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);
        $this->budgetService->deleteBudget($budget);
        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }
}
