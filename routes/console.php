<?php

use App\Console\Commands\DeactivateExpiredDeals;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs every minute, checks for deals whose ends_at has passed, and
// flips is_active off. This is what makes an expired Flash Deal /
// promo code / etc. actually turn itself off without you touching it.
Schedule::command(DeactivateExpiredDeals::class)->everyMinute();
