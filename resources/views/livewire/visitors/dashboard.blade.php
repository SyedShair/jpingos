<div wire:poll.30s.visible="refresh">

    {{-- =================================================
         PAGE HEADER (same markup as Orders Manager)
    ================================================== --}}

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Visitors</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visitors</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- =================================================
         RANGE FILTER
    ================================================== --}}

    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small me-1">Range:</span>
        @foreach ([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'] as $value => $label)
            <button type="button" class="btn btn-sm {{ $days === $value ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('days', {{ $value }})">
                {{ $label }}
            </button>
        @endforeach
        <span class="text-muted small ms-2">Live · refreshes every 30 seconds</span>
    </div>

    {{-- =================================================
         KPI CARDS
    ================================================== --}}

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card mb-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-success text-success" style="width:48px;height:48px;">
                        <i class="material-icons-outlined">sensors</i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small">
                            <span class="spinner-grow spinner-grow-sm text-success me-1" style="width:.55rem;height:.55rem;" role="status"></span>Online now
                        </p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['online']) }}</h4>
                        <span class="text-muted small">active in the last 5 minutes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card mb-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:48px;height:48px;">
                        <i class="material-icons-outlined">today</i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small">Today</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['today_visitors']) }}</h4>
                        <span class="text-muted small">{{ number_format($stats['today_views']) }} page views</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card mb-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-info text-info" style="width:48px;height:48px;">
                        <i class="material-icons-outlined">groups</i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small">Unique visitors ({{ $days }}d)</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['visitors']) }}</h4>
                        <span class="text-muted small">{{ number_format($stats['new']) }} new · {{ number_format($stats['returning']) }} returning</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card mb-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-warning text-warning" style="width:48px;height:48px;">
                        <i class="material-icons-outlined">visibility</i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small">Page views ({{ $days }}d)</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['views']) }}</h4>
                        <span class="text-muted small">{{ $stats['visitors'] ? number_format($stats['views'] / $stats['visitors'], 1) : '0.0' }} pages per visitor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================================================
         TRAFFIC TREND
         wire:ignore stops Livewire replacing the canvas on each poll;
         the script below redraws it when the `chart` property changes.
    ================================================== --}}

    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Traffic trend</h5>
            <div wire:ignore style="position:relative;height:280px;">
                <canvas id="vz-chart"></canvas>
            </div>
        </div>
    </div>

    {{-- =================================================
         GEOGRAPHY: COUNTRY / CITY / AREA
    ================================================== --}}

    <div class="row g-3 mb-3">

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top countries</h5>
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Country</th><th class="text-end">Visitors</th><th class="text-end">Views</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($countries as $c)
                                    <tr>
                                        <td>{{ \App\Models\Visit::flag($c->country_code) }} {{ $c->country }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($c->visitors) }}</td>
                                        <td class="text-end text-muted">{{ number_format($c->views) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No location data yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top cities</h5>
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>City</th><th class="text-end">Visitors</th><th class="text-end">Views</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($cities as $c)
                                    <tr>
                                        <td>
                                            {{ $c->city }}
                                            <br><span class="text-muted small">{{ \App\Models\Visit::flag($c->country_code) }} {{ $c->country }}</span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format($c->visitors) }}</td>
                                        <td class="text-end text-muted">{{ number_format($c->views) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No city data yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top areas</h5>
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Area</th><th class="text-end">Visitors</th><th class="text-end">Views</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($areas as $a)
                                    <tr>
                                        <td>
                                            {{ $a->area_label }}
                                            <br><span class="text-muted small">{{ $a->city ?: '—' }}</span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format($a->visitors) }}</td>
                                        <td class="text-end text-muted">{{ number_format($a->views) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No area data yet. Areas appear when visitors allow location or the lookup returns a postcode.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- =================================================
         PAGES / REFERRERS / DEVICES
    ================================================== --}}

    <div class="row g-3 mb-3">

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top pages</h5>
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th class="text-end">Views</th><th class="text-end">Visitors</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($pages as $p)
                                    <tr>
                                        <td><span class="text-truncate d-inline-block align-bottom" style="max-width:200px;" title="{{ $p->path }}">{{ $p->path }}</span></td>
                                        <td class="text-end fw-semibold">{{ number_format($p->views) }}</td>
                                        <td class="text-end text-muted">{{ number_format($p->visitors) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No page views yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Referrers</h5>
                    <div class="table-responsive white-space-nowrap">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Source</th><th class="text-end">Visitors</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($referrers as $r)
                                    <tr>
                                        <td>{{ $r->referrer_host }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($r->visitors) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-4">All traffic is direct so far.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Devices</h5>
                    @php $deviceTotal = max($devices->sum('visitors'), 1); @endphp
                    @forelse ($devices as $d)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-capitalize">{{ $d->device_type }}</span>
                                <span class="text-muted small">{{ round($d->visitors / $deviceTotal * 100) }}% · {{ number_format($d->visitors) }}</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $d->visitors / $deviceTotal * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No device data yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- =================================================
         LATEST VISITS
    ================================================== --}}

    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Latest visits</h5>
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>When</th>
                            <th>IP</th>
                            <th>Location</th>
                            <th>Area</th>
                            <th>Page</th>
                            <th>Device</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $v)
                            <tr wire:key="visit-row-{{ $v->id }}">
                                <td><span class="text-muted small">{{ $v->created_at->diffForHumans() }}</span></td>
                                <td><span class="text-muted small">{{ $v->ip_address ?: '—' }}</span></td>
                                <td>
                                    {{ \App\Models\Visit::flag($v->country_code) }}
                                    {{ $v->city ?: '—' }}{{ $v->country ? ', ' . $v->country : '' }}
                                    @if ($v->location_source === 'browser')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">precise</span>
                                    @elseif ($v->location_source === 'ip')
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle ms-1">approx</span>
                                    @endif
                                </td>
                                <td>{{ $v->area ?: ($v->postal_code ?: '—') }}</td>
                                <td><span class="text-truncate d-inline-block align-bottom" style="max-width:220px;" title="{{ $v->path }}">{{ $v->path }}</span></td>
                                <td class="text-capitalize">{{ $v->device_type }}{{ $v->browser ? ' · ' . $v->browser : '' }}</td>
                                <td>{{ $v->referrer_host ?: 'Direct' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    No visits yet. Open your storefront in a private window to see the first one appear.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endassets

@script
<script>
    const canvas = $wire.$el.querySelector('#vz-chart');
    let chart = null;

    const draw = () => {
        // Clone: $wire.chart is a reactive proxy and Chart.js needs plain arrays.
        const d = JSON.parse(JSON.stringify($wire.chart));

        if (chart) {
            chart.data.labels = d.labels;
            chart.data.datasets[0].data = d.visitors;
            chart.data.datasets[1].data = d.views;
            chart.update();
            return;
        }

        // Follow the admin theme (Maxton's default is a dark blue theme): use the page's text colour.
        Chart.defaults.color = getComputedStyle(document.body).color;
        Chart.defaults.borderColor = 'rgba(128,128,128,.2)';

        chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: d.labels,
                datasets: [
                    {
                        label: 'Unique visitors',
                        data: d.visitors,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                    },
                    {
                        label: 'Page views',
                        data: d.views,
                        borderColor: '#9ca3af',
                        borderDash: [5, 4],
                        fill: false,
                        tension: 0.35,
                        pointRadius: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 12 } },
                },
            },
        });
    };

    draw();

    // Fires whenever wire:poll (or the range buttons) change the chart data.
    $wire.$watch('chart', draw);

    document.addEventListener('livewire:navigating', () => chart && chart.destroy(), { once: true });
</script>
@endscript
