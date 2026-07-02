<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DailyReportService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendDailyWhatsAppReport extends Command
{
    protected $signature = 'report:daily-whatsapp
        {--date= : Target date (default: yesterday)}
        {--dry-run : Preview without sending}';

    protected $description = 'Send daily financial report to WhatsApp via Fonnte';

    public function handle(
        DailyReportService $reportService,
        WhatsAppService $whatsAppService,
    ): int {
        $date = $this->option('date') ?? now()->subDay()->format('Y-m-d');
        $dryRun = $this->option('dry-run');
        $sent = 0;
        $skipped = 0;
        $errors = 0;

        $users = User::whereNotNull('preferences')->get();

        foreach ($users as $user) {
            $token = $user->preference('fonnte_token');
            $target = $user->preference('whatsapp_target');

            if (!$token || !$target) {
                $skipped++;
                continue;
            }

            $report = $reportService->generate($user, $date);

            if ($dryRun) {
                $this->line(sprintf('[DRY-RUN] User: %s (%s)', $user->name, $target));
                $this->line($report);
                $this->newLine();
            } else {
                $ok = $whatsAppService->send($token, $target, $report);
                if ($ok) {
                    $this->line(sprintf('Sent to %s (%s)', $user->name, $target));
                    $sent++;
                } else {
                    $this->error(sprintf('Failed to send to %s (%s)', $user->name, $target));
                    $errors++;
                }
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete. {$skipped} skipped (no config).");
        } else {
            $this->info("Done. {$sent} sent, {$skipped} skipped, {$errors} errors.");
        }

        return 0;
    }
}
