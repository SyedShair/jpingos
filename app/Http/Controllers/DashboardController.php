<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Visit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Admin dashboard: every number on the page comes from your real orders,
 * customers and tracked visits. Nothing is hard-coded.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Range buttons at the top of the page: ?range=7 | 14 | 30
        $days = in_array((int) $request->query('range'), [7, 14, 30], true) ? (int) $request->query('range') : 14;

        $today    = CarbonImmutable::today();
        $tomorrow = $today->addDay();
        $from     = $today->subDays($days - 1);      // start of the selected period
        $prevFrom = $from->subDays($days);           // start of the previous, equally long period

        // Cancelled orders don't count as sales.
        $valid = fn () => Order::query()->where('status', '!=', 'cancelled');

        // ------------------------------------------------------------------ SALES / ORDERS
        $salesToday     = (float) $valid()->where('created_at', '>=', $today)->sum('total');
        $salesYesterday = (float) $valid()->where('created_at', '>=', $today->subDay())->where('created_at', '<', $today)->sum('total');

        $ordersToday    = $valid()->where('created_at', '>=', $today)->count();
        $completedToday = Order::where('status', 'completed')->where('created_at', '>=', $today)->count();
        $pendingOrders  = Order::where('status', 'pending')->count();

        $revenue      = (float) $valid()->where('created_at', '>=', $from)->sum('total');
        $prevRevenue  = (float) $valid()->where('created_at', '>=', $prevFrom)->where('created_at', '<', $from)->sum('total');
        $orderCount   = $valid()->where('created_at', '>=', $from)->count();
        $prevOrders   = $valid()->where('created_at', '>=', $prevFrom)->where('created_at', '<', $from)->count();
        $aov          = $orderCount ? $revenue / $orderCount : 0.0;

        $orderRows = $valid()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('d')->get()->keyBy('d');

        $statusCounts = Order::where('created_at', '>=', $from)
            ->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $typeCounts = Order::where('created_at', '>=', $from)
            ->selectRaw('order_type, COUNT(*) as c')->groupBy('order_type')->pluck('c', 'order_type');

        $recentOrders = Order::with('latestPayment')->latest()->limit(8)->get();

        // ------------------------------------------------------------------ VISITORS (from the tracking middleware)
        $visitorsToday = Visit::where('created_at', '>=', $today)->distinct()->count('visitor_uid');
        $onlineNow     = Visit::where('created_at', '>=', now()->subMinutes(5))->distinct()->count('visitor_uid');
        $visitors      = Visit::where('created_at', '>=', $from)->distinct()->count('visitor_uid');
        $prevVisitors  = Visit::where('created_at', '>=', $prevFrom)->where('created_at', '<', $from)->distinct()->count('visitor_uid');
        $pageViews     = Visit::where('created_at', '>=', $from)->count();

        $visitRows = Visit::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as views, COUNT(DISTINCT visitor_uid) as visitors')
            ->groupBy('d')->get()->keyBy('d');

        $deviceCounts = Visit::where('created_at', '>=', $from)
            ->selectRaw('device_type, COUNT(DISTINCT visitor_uid) as v')->groupBy('device_type')->pluck('v', 'device_type');
        $devices = [
            'desktop' => (int) ($deviceCounts['desktop'] ?? 0),
            'tablet'  => (int) ($deviceCounts['tablet'] ?? 0),
            'mobile'  => (int) ($deviceCounts['mobile'] ?? 0),
        ];

        $topCountries = Visit::where('created_at', '>=', $from)->whereNotNull('country')
            ->selectRaw('country, country_code, COUNT(DISTINCT visitor_uid) as v')
            ->groupBy('country', 'country_code')->orderByDesc('v')->limit(4)->get();

        // Traffic sources: external referrers, plus "Direct" = visitors who never arrived from another site.
        $referred = Visit::where('created_at', '>=', $from)->whereNotNull('referrer_host')->distinct()->count('visitor_uid');
        $sources = Visit::where('created_at', '>=', $from)->whereNotNull('referrer_host')
            ->selectRaw('referrer_host, COUNT(DISTINCT visitor_uid) as v')
            ->groupBy('referrer_host')->orderByDesc('v')->limit(5)->get()
            ->map(fn ($r) => ['label' => $r->referrer_host, 'visitors' => (int) $r->v])
            ->values()->all();
        array_unshift($sources, ['label' => 'Direct', 'visitors' => max($visitors - $referred, 0)]);

        // ------------------------------------------------------------------ CUSTOMERS (model taken from your `customer` auth guard)
        $customers = null;
        $customerModel = config('auth.providers.' . config('auth.guards.customer.provider') . '.model');

        if ($customerModel && class_exists($customerModel)) {
            $customerRows = $customerModel::where('created_at', '>=', $from)
                ->selectRaw('DATE(created_at) as d, COUNT(*) as c')->groupBy('d')->pluck('c', 'd');

            $customers = [
                'total'    => $customerModel::count(),
                'period'   => $customerModel::where('created_at', '>=', $from)->count(),
                'previous' => $customerModel::where('created_at', '>=', $prevFrom)->where('created_at', '<', $from)->count(),
                'series'   => $this->fill($from, $days, fn ($d) => (float) ($customerRows[$d] ?? 0)),
                'latest'   => $customerModel::latest()->limit(6)->get(),
            ];
        }

        // ------------------------------------------------------------------ chart payload
        $charts = [
            'labels'    => collect(range(0, $days - 1))->map(fn ($i) => $from->addDays($i)->format('j M'))->all(),
            'revenue'   => $this->fill($from, $days, fn ($d) => round((float) ($orderRows->get($d)->revenue ?? 0), 2)),
            'orders'    => $this->fill($from, $days, fn ($d) => (float) ($orderRows->get($d)->orders ?? 0)),
            'visitors'  => $this->fill($from, $days, fn ($d) => (float) ($visitRows->get($d)->visitors ?? 0)),
            'views'     => $this->fill($from, $days, fn ($d) => (float) ($visitRows->get($d)->views ?? 0)),
            'customers' => $customers['series'] ?? [],
            'devices'   => ['labels' => ['Desktop', 'Tablet', 'Mobile'], 'values' => array_values($devices)],
        ];

        // NOTE: change 'dashboard' if your current controller returns a different view name.
        return view('dashboard', [
            'days'           => $days,
            'salesToday'     => $salesToday,
            'salesYesterday' => $salesYesterday,
            'salesChange'    => $this->pct($salesToday, $salesYesterday),
            'ordersToday'    => $ordersToday,
            'completedToday' => $completedToday,
            'pendingOrders'  => $pendingOrders,
            'revenue'        => $revenue,
            'revenueChange'  => $this->pct($revenue, $prevRevenue),
            'orderCount'     => $orderCount,
            'ordersChange'   => $this->pct($orderCount, $prevOrders),
            'aov'            => $aov,
            'statusCounts'   => $statusCounts,
            'typeCounts'     => $typeCounts,
            'recentOrders'   => $recentOrders,
            'visitorsToday'  => $visitorsToday,
            'onlineNow'      => $onlineNow,
            'visitors'       => $visitors,
            'visitorsChange' => $this->pct($visitors, $prevVisitors),
            'pageViews'      => $pageViews,
            'devices'        => $devices,
            'deviceTotal'    => array_sum($devices),
            'topCountries'   => $topCountries,
            'sources'        => $sources,
            'customers'      => $customers,
            'customersChange'=> $customers ? $this->pct($customers['period'], $customers['previous']) : null,
            'charts'         => $charts,
        ]);
    }

    /** Percentage change, or null when there is nothing to compare against. */
    private function pct($now, $before): ?float
    {
        return $before > 0 ? round(($now - $before) / $before * 100, 1) : null;
    }

    /** One value per day of the period, zero-filled. */
    private function fill(CarbonImmutable $from, int $days, callable $value): array
    {
        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $out[] = $value($from->addDays($i)->toDateString());
        }

        return $out;
    }
}
