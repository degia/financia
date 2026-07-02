<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DailyReportService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyWhatsAppReport extends Command
{
    protected $signature = 'report:daily-whatsapp
        {--date= : Target date (default: yesterday)}
        {--force : Send regardless of user time preference}
        {--dry-run : Preview without sending}';

    protected $description = 'Send daily financial report to WhatsApp via Fonnte';

    public function handle(
        DailyReportService $reportService,
        WhatsAppService $whatsAppService,
    ): int {
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
            $token = $user->preference('fonnte_token');
            $target = $user->preference('whatsapp_target');

            if (!$token || !$target) {
                $skipped++;
                continue;
            }

            $preferredTime = $user->preference('whatsapp_time', '07:00');

            if (!$force) {
                if (!$this->timeMatches($currentTime, $preferredTime)) {
                    continue;
                }

                $lastSent = $user->preference('whatsapp_last_sent');
                if ($lastSent === $date) {
                    continue;
                }
            }

            $sections = $user->preference('whatsapp_sections', ['income', 'expense', 'categories', 'accounts', 'net']);
            $customHeader = $user->preference('whatsapp_custom_header');
            $customFooter = $user->preference('whatsapp_custom_footer');

            $report = $reportService->generate($user, $date, $sections, $customHeader, $customFooter);

            if ($dryRun) {
                $this->line(sprintf('[DRY-RUN] User: %s (%s) @ %s', $user->name, $target, $preferredTime));
                $this->line($report);
                $this->newLine();
                $sent++;
            } else {
                $ok = $whatsAppService->send($token, $target, $report);
                if ($ok) {
                    $user->setPreference('whatsapp_last_sent', $date);
                    $user->save();
                    $this->line(sprintf('Sent to %s (%s)', $user->name, $target));
                    $sent++;
                } else {
                    $this->error(sprintf('Failed to send to %s (%s)', $user->name, $target));
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
}
