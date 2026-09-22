<?php

namespace App\Livewire\Visitors;

use App\Models\Visit;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

// IMPORTANT: use the SAME layout your Orders Manager uses.
// Open app/Livewire/Orders/Manager.php and copy its #[Layout('...')] value here.
#[Layout('layouts.app')]
#[Title('Visitors')]
class Dashboard extends Component
{
    #[Url(as: 'range')]
    public int $days = 30;

    /** Chart data lives in a public property so the browser can watch it and redraw on every poll. */
    public array $chart = ['labels' => [], 'visitors' => [], 'views' => []];

    public function mount(): void
    {
        $this->normaliseRange();
        $this->loadChart();
    }

    public function updatedDays(): void
    {
        $this->normaliseRange();
        $this->loadChart();
    }

    /** Called by wire:poll every 30s. The component re-renders after it, refreshing every card and table. */
    public function refresh(): void
    {
        $this->loadChart();
    }

    private function normaliseRange(): void
    {
        $this->days = in_array((int) $this->days, [7, 30, 90], true) ? (int) $this->days : 30;
    }

    private function loadChart(): void
    {
        $from = CarbonImmutable::today()->subDays($this->days - 1);

        $rows = Visit::query()
            ->selectRaw('DATE(created_at) as visit_day, COUNT(*) as views, COUNT(DISTINCT visitor_uid) as visitors')
            ->where('created_at', '>=', $from)
            ->groupBy('visit_day')
            ->get()
            ->keyBy('visit_day');

        $labels = $visitors = $views = [];

        for ($i = 0; $i < $this->days; $i++) {
            $d = $from->addDays($i);
            $r = $rows->get($d->toDateString());

            $labels[]   = $d->format('j M');
            $visitors[] = (int) ($r->visitors ?? 0);
            $views[]    = (int) ($r->views ?? 0);
        }

        $this->chart = compact('labels', 'visitors', 'views');
    }

    public function render()
    {
        $from       = CarbonImmutable::today()->subDays($this->days - 1);
        $todayStart = CarbonImmutable::today();

        $periodVisitors = Visit::where('created_at', '>=', $from)->distinct()->count('visitor_uid');

        // Visitors whose very first visit falls inside the selected period
        $newVisitors = DB::query()
            ->fromSub(
                Visit::query()->select('visitor_uid')->selectRaw('MIN(created_at) as first_seen')->groupBy('visitor_uid'),
                't'
            )
            ->where('first_seen', '>=', $from)
            ->count();

        $stats = [
            'online'         => Visit::where('created_at', '>=', now()->subMinutes(5))->distinct()->count('visitor_uid'),
            'today_visitors' => Visit::where('created_at', '>=', $todayStart)->distinct()->count('visitor_uid'),
            'today_views'    => Visit::where('created_at', '>=', $todayStart)->count(),
            'visitors'       => $periodVisitors,
            'views'          => Visit::where('created_at', '>=', $from)->count(),
            'new'            => $newVisitors,
            'returning'      => max($periodVisitors - $newVisitors, 0),
        ];

        $period = fn () => Visit::query()->where('created_at', '>=', $from);

        $countries = $period()->whereNotNull('country')
            ->selectRaw('country, country_code, COUNT(DISTINCT visitor_uid) as visitors, COUNT(*) as views')
            ->groupBy('country', 'country_code')
            ->orderByDesc('visitors')->limit(10)->get();

        $cities = $period()->whereNotNull('city')
            ->selectRaw('city, country, country_code, COUNT(DISTINCT visitor_uid) as visitors, COUNT(*) as views')
            ->groupBy('city', 'country', 'country_code')
            ->orderByDesc('visitors')->limit(10)->get();

        $areas = $period()->whereRaw('COALESCE(area, postal_code) IS NOT NULL')
            ->selectRaw('COALESCE(area, postal_code) as area_label, city, COUNT(DISTINCT visitor_uid) as visitors, COUNT(*) as views')
            ->groupBy('area_label', 'city')
            ->orderByDesc('visitors')->limit(10)->get();

        $pages = $period()
            ->selectRaw('path, COUNT(*) as views, COUNT(DISTINCT visitor_uid) as visitors')
            ->groupBy('path')->orderByDesc('views')->limit(10)->get();

        $referrers = $period()->whereNotNull('referrer_host')
            ->selectRaw('referrer_host, COUNT(DISTINCT visitor_uid) as visitors')
            ->groupBy('referrer_host')->orderByDesc('visitors')->limit(8)->get();

        $devices = $period()
            ->selectRaw('device_type, COUNT(DISTINCT visitor_uid) as visitors')
            ->groupBy('device_type')->orderByDesc('visitors')->get();

        $recent = Visit::query()->orderByDesc('id')->limit(15)->get();

        return view('livewire.visitors.dashboard', compact(
            'stats', 'countries', 'cities', 'areas', 'pages', 'referrers', 'devices', 'recent'
        ));
    }
}
