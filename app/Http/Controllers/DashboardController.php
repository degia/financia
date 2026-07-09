<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $selectedCategories = $request->get('categories', []);

        $selectedDate = $request->get('selected_date', now()->format('Y-m-d'));

        $dayIncome = (float) $user->transactions()
            ->where('type', 'income')
            ->whereNull('transfer_id')
            ->whereDate('date', $selectedDate)
            ->sum('amount');

        $dayExpense = (float) $user->transactions()
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            })
            ->whereDate('date', $selectedDate)
            ->sum('amount');

        $summary = $this->dashboardService->getSummary($user, $month, $year);
        $monthlyChart = $this->dashboardService->getMonthlyChart($user, $year);
        $dailyChart = $this->dashboardService->getDailyChart($user, $month, $year);
        $categoryBreakdown = $this->dashboardService->getCategoryBreakdown($user, $month, $year, $selectedCategories);
        $budgetProgress = $this->dashboardService->getBudgetsProgress($user, $month, $year);
        $savings = $this->dashboardService->getSavingsSummary($user, $month, $year);
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $categories = Category::forUser($user->id)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('dashboard', compact(
            'summary',
            'monthlyChart',
            'dailyChart',
            'categoryBreakdown',
            'budgetProgress',
            'savings',
            'recentTransactions',
            'categories',
            'selectedCategories',
            'month',
            'year',
            'selectedDate',
            'dayIncome',
            'dayExpense',
        ));
    }
}
