<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Illuminate\Console\Command;

class PruneVisits extends Command
{
    protected $signature = 'visits:prune {--days= : Override the retention period from config/tracking.php}';

    protected $description = 'Delete visitor rows older than the retention period';

    public function handle(): int
    {
        $days    = (int) ($this->option('days') ?: config('tracking.retention_days'));
        $deleted = Visit::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} visits older than {$days} days.");

        return self::SUCCESS;
    }
}
