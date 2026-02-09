<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MeetingLog;
use App\Services\ZoomSyncService;

class ZoomSyncMtgs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:sync-mtgs {--from= : Start Date (YYYY-MM-DD)} {--to= : End Date (YYYY-MM-DD)} {--force : Force sync even if synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync past meeting details and participants from Zoom';

    /**
     * Execute the console command.
     */
    public function handle(ZoomSyncService $syncService)
    {
        $from = $this->option('from') ?? now()->subDays(1)->format('Y-m-d');
        $to = $this->option('to') ?? now()->format('Y-m-d');
        $force = $this->option('force');

        $this->info("Syncing Zoom Mtgs from $from to $to...");

        $query = MeetingLog::whereNotNull('zoom_meeting_id')
            ->whereDate('started_at', '>=', $from)
            ->whereDate('started_at', '<=', $to)
            ->where('started_at', '<', now()); // Only past meetings

        if (!$force) {
            $query->where(function ($q) {
                $q->where('zoom_sync_status', '!=', 'synced')
                    ->orWhereNull('zoom_sync_status');
            });
        }

        $logs = $query->get();
        $count = $logs->count();
        $this->info("Found $count meetings to sync.");

        if ($count === 0) {
            $this->info('No meetings found to sync.');
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($logs as $log) {
            if (str_starts_with($log->zoom_meeting_id, 'mock_')) {
                // Skip mock logs
                $log->zoom_sync_status = 'failed';
                $log->zoom_sync_error = 'Mock meeting cannot be synced.';
                $log->save();
                $bar->advance();
                continue;
            }

            $syncService->syncMeeting($log);

            usleep(200000); // 0.2s
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed.');
    }
}
