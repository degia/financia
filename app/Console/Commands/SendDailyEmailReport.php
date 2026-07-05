<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Models\User;
use App\Services\EmailReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyEmailReport extends Command
{
    protected $signature = 'report:daily-email
        {--date= : Target date (default: yesterday)}
        {--force : Send regardless of user preferences}
        {--dry-run : Preview without sending}';

    protected $description = 'Send daily financial report via email';

    public function handle(EmailReportService $reportService): int
    {
        $date = $this->option('date') ?? now()->subDay()->format('Y-m-d');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        $sent = 0;
        $skipped = 0;
        $errors = 0;

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $users = User::whereNotNull('preferences')->get();

        foreach ($users as $user) {
            $enabled = $user->preference('email_report_enabled', false);
            $email = $user->email;

            if (!$enabled || !$email) {
                $skipped++;
                continue;
            }

            $preferredTime = $user->preference('email_report_time', '07:00');

            if (!$force) {
                if (!$this->timeMatches($currentTime, $preferredTime)) {
                    continue;
                }

                $lastSent = $user->preference('email_report_last_sent');
                if ($lastSent === $date) {
                    continue;
                }
            }

            $currency = $user->currency_preference ?? 'USD';
            $symbol = $this->currencySymbol($currency);
            $sections = $user->preference('email_report_sections', ['income', 'expense', 'net', 'categories', 'accounts', 'transactions', 'budgets']);

            $data = $reportService->gatherData($user, $date, $sections);

            if ($dryRun) {
                $this->line(sprintf('[DRY-RUN] User: %s (%s) @ %s', $user->name, $email, $preferredTime));
                $this->line('Sections: ' . implode(', ', $sections));
                $this->line('Income: ' . ($data['income'] ?? 0));
                $this->line('Expense: ' . ($data['expense'] ?? 0));
                $this->newLine();
                $sent++;
            } else {
                try {
                    Mail::to($email)->send(new DailyReportMail($user, $date, $data, $symbol));
                    $user->setPreference('email_report_last_sent', $date);
                    $user->save();
                    $this->line(sprintf('Sent to %s (%s)', $user->name, $email));
                    $sent++;
                } catch (\Exception $e) {
                    $this->error(sprintf('Failed to send to %s (%s): %s', $user->name, $email, $e->getMessage()));
                    $errors++;
                }
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete. {$sent} would be sent, {$skipped} skipped (no config).");
        } else {
            $this->info("Done. {$sent} sent, {$skipped} skipped, {$errors} errors.");
        }

        return 0;
    }

    protected function timeMatches(string $currentTime, string $preferredTime): bool
    {
        $diff = abs(strtotime($currentTime) - strtotime($preferredTime));
        return $diff <= 900;
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
