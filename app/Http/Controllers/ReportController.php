<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        $accounts = Account::where('user_id', $user->id)->orderBy('name')->get();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();

        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'category_id']);
        $filters['start_date'] = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $filters['end_date'] = $filters['end_date'] ?? now()->format('Y-m-d');

        $data = $this->reportService->getTransactionsReport($user, $filters);
        $categoryReport = $this->reportService->getCategoryReport($user, $filters['start_date'], $filters['end_date']);

        return view('reports.index', array_merge($data, [
            'accounts' => $accounts,
            'categories' => $categories,
            'categoryReport' => $categoryReport,
        ]));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'category_id']);
        $filters['start_date'] = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $filters['end_date'] = $filters['end_date'] ?? now()->format('Y-m-d');

        $data = $this->reportService->getTransactionsReport($user, $filters);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="report_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Description', 'Category', 'Account', 'Type', 'Amount']);

            foreach ($data['transactions'] as $t) {
                fputcsv($handle, [
                    $t->date->format('Y-m-d'),
                    $t->description,
                    $t->category->name,
                    $t->account->name,
                    ucfirst($t->type),
                    ($t->type === 'income' ? '' : '-') . number_format($t->amount, 2, '.', ''),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'category_id']);
        $filters['start_date'] = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $filters['end_date'] = $filters['end_date'] ?? now()->format('Y-m-d');

        $data = $this->reportService->getTransactionsReport($user, $filters);

        $pdf = Pdf::loadView('reports.pdf', compact('data', 'filters'));

        return $pdf->download('report_' . now()->format('Ymd_His') . '.pdf');
    }
}
