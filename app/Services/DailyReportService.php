<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DailyReportService
{
    public function generate(User $user, string $date): string
    {
        $dateObj = \Carbon\Carbon::parse($date);
        $formattedDate = $dateObj->format('d M Y');
        $dayName = $dateObj->format('l');

        $income = (float) $user->transactions()
            ->where('type', 'income')
            ->whereNull('transfer_id')
            ->whereDate('date', $date)
            ->sum('amount');

        $expense = (float) $user->transactions()
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            })
            ->whereDate('date', $date)
            ->sum('amount');

        $net = $income - $expense;

        $incomeCategories = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'income')
            ->whereNull('transfer_id')
            ->whereDate('date', $date)
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $expenseCategories = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->whereNull('transfer_id')->orWhere('is_savings', true);
            })
            ->whereDate('date', $date)
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $accounts = $user->accounts()
            ->where('current_balance', '>', 0)
            ->orderBy('name')
            ->get(['name', 'current_balance', 'category']);

        $currency = $user->currency_preference ?? 'USD';
        $symbol = $this->currencySymbol($currency);

        $lines = [];
        $lines[] = '▬▬ FINANCIA REPORT ▬▬';
        $lines[] = '📅 ' . $dayName . ', ' . $formattedDate;
        $lines[] = '';

        $lines[] = '💵 *PEMASUKAN:* ' . $symbol . $this->format($income);
        if ($incomeCategories->isNotEmpty()) {
            foreach ($incomeCategories as $cat) {
                $lines[] = '  • ' . ($cat->category->name ?? 'Unknown') . ': ' . $symbol . $this->format($cat->total);
            }
        } else {
            $lines[] = '  _(tidak ada)_';
        }

        $lines[] = '';
        $lines[] = '💳 *PENGELUARAN:* ' . $symbol . $this->format($expense);
        if ($expenseCategories->isNotEmpty()) {
            foreach ($expenseCategories as $cat) {
                $lines[] = '  • ' . ($cat->category->name ?? 'Unknown') . ': ' . $symbol . $this->format($cat->total);
            }
        } else {
            $lines[] = '  _(tidak ada)_';
        }

        $lines[] = '';
        $netSign = $net >= 0 ? '+' : '';
        $netEmoji = $net > 0 ? '🟢' : ($net < 0 ? '🔴' : '⚪');
        $lines[] = $netEmoji . ' *NET:* ' . $symbol . $netSign . $this->format($net);

        if ($accounts->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '💰 *SALDO AKUN:*';
            foreach ($accounts as $acct) {
                $acctLabel = $acct->name;
                if ($acct->category === 'savings') {
                    $acctLabel .= ' 🏦';
                }
                $lines[] = '  • ' . $acctLabel . ': ' . $symbol . $this->format($acct->current_balance);
            }
        }

        $lines[] = '';
        $lines[] = '▬▬▬▬▬▬▬▬▬▬▬▬▬';
        $lines[] = 'Dikirim otomatis oleh Financia';

        return implode("\n", $lines);
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
