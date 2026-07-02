<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DailyReportService
{
    public function generate(User $user, string $date, array $sections = [], ?string $customHeader = null, ?string $customFooter = null): string
    {
        $defaultSections = ['income', 'expense', 'categories', 'accounts', 'net'];
        $sections = !empty($sections) ? $sections : $defaultSections;
        $showCategories = in_array('categories', $sections);

        $dateObj = \Carbon\Carbon::parse($date);
        $formattedDate = $dateObj->format('d M Y');
        $dayName = $dateObj->format('l');

        $currency = $user->currency_preference ?? 'USD';
        $symbol = $this->currencySymbol($currency);

        $lines = [];

        if ($customHeader) {
            $lines[] = $customHeader;
            $lines[] = '';
        }

        $lines[] = '▬▬ FINANCIA REPORT ▬▬';
        $lines[] = '📅 ' . $dayName . ', ' . $formattedDate;
        $lines[] = '';

        if (in_array('income', $sections)) {
            $income = (float) $user->transactions()
                ->where('type', 'income')
                ->whereNull('transfer_id')
                ->whereDate('date', $date)
                ->sum('amount');

            $lines[] = '💵 *PEMASUKAN:* ' . $symbol . $this->format($income);

            if ($showCategories) {
                $this->appendCategoryBreakdown($lines, $user, $date, 'income', $symbol);
            }

            $lines[] = '';
        }

        if (in_array('expense', $sections)) {
            $expense = (float) $user->transactions()
                ->where('type', 'expense')
                ->where(function ($q) {
                    $q->whereNull('transfer_id')->orWhere('is_savings', true);
                })
                ->whereDate('date', $date)
                ->sum('amount');

            $lines[] = '💳 *PENGELUARAN:* ' . $symbol . $this->format($expense);

            if ($showCategories) {
                $this->appendCategoryBreakdown($lines, $user, $date, 'expense', $symbol);
            }

            $lines[] = '';
        }

        if (in_array('net', $sections)) {
            $incomeTotal = (float) $user->transactions()
                ->where('type', 'income')
                ->whereNull('transfer_id')
                ->whereDate('date', $date)
                ->sum('amount');

            $expenseTotal = (float) $user->transactions()
                ->where('type', 'expense')
                ->where(function ($q) {
                    $q->whereNull('transfer_id')->orWhere('is_savings', true);
                })
                ->whereDate('date', $date)
                ->sum('amount');

            $net = $incomeTotal - $expenseTotal;
            $netSign = $net >= 0 ? '+' : '';
            $netEmoji = $net > 0 ? '🟢' : ($net < 0 ? '🔴' : '⚪');
            $lines[] = $netEmoji . ' *NET:* ' . $symbol . $netSign . $this->format($net);
            $lines[] = '';
        }

        if (in_array('accounts', $sections)) {
            $accounts = $user->accounts()
                ->where('current_balance', '>', 0)
                ->orderBy('name')
                ->get(['name', 'current_balance', 'category']);

            if ($accounts->isNotEmpty()) {
                $lines[] = '💰 *SALDO AKUN:*';
                foreach ($accounts as $acct) {
                    $acctLabel = $acct->name;
                    if ($acct->category === 'savings') {
                        $acctLabel .= ' 🏦';
                    }
                    $lines[] = '  • ' . $acctLabel . ': ' . $symbol . $this->format($acct->current_balance);
                }
                $lines[] = '';
            }
        }

        $lines[] = '▬▬▬▬▬▬▬▬▬▬▬▬▬';
        $lines[] = 'Dikirim otomatis oleh Financia';

        if ($customFooter) {
            $lines[] = '';
            $lines[] = $customFooter;
        }

        return implode("\n", $lines);
    }

    protected function appendCategoryBreakdown(array &$lines, User $user, string $date, string $type, string $symbol): void
    {
        $query = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', $type)
            ->whereDate('date', $date);

        if ($type === 'expense') {
            $query->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            });
        } else {
            $query->whereNull('transfer_id');
        }

        $rows = $query->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        if ($rows->isNotEmpty()) {
            foreach ($rows as $cat) {
                $lines[] = '  • ' . ($cat->category->name ?? 'Unknown') . ': ' . $symbol . $this->format($cat->total);
            }
        } else {
            $lines[] = '  _(tidak ada)_';
        }
    }

    protected function format(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    protected function currencySymbol(string $currency): string
    {
        return match ($currency) {
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'MYR' => 'RM',
            default => $currency . ' ',
        };
    }
}
