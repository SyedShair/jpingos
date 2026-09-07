<?php

namespace App\Console\Commands;

use App\Models\Deal;
use Illuminate\Console\Command;

class DeactivateExpiredDeals extends Command
{
    protected $signature = 'deals:deactivate-expired';

    protected $description = 'Turn off any deal whose end date/time has passed, so it stops showing as active.';

    public function handle(): int
    {
        $expired = Deal::query()
            ->where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($expired as $deal) {
            $deal->update(['is_active' => false]);
            $this->line("Deactivated: {$deal->name} (ended {$deal->ends_at->format('M j, Y H:i')})");
        }

        $this->info($expired->isEmpty()
            ? 'No expired deals found.'
            : "Deactivated {$expired->count()} expired deal(s).");

        return self::SUCCESS;
    }
}
