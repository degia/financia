<?php

namespace App\Http\Controllers;

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

        $summary = $this->dashboardService->getSummary($user, $month, $year);
        $monthlyChart = $this->dashboardService->getMonthlyChart($user, $year);
        $dailyChart = $this->dashboardService->getDailyChart($user, $month, $year);
        $categoryBreakdown = $this->dashboardService->getCategoryBreakdown($user, $month, $year);
        $budgetProgress = $this->dashboardService->getBudgetsProgress($user, $month, $year);
        $savings = $this->dashboardService->getSavingsSummary($user, $month, $year);
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'summary',
            'monthlyChart',
            'dailyChart',
            'categoryBreakdown',
            'budgetProgress',
            'savings',
            'recentTransactions',
            'month',
            'year'
        ));
    }
}
