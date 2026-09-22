@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
  @php
    // Small helpers used throughout the page
    $money = fn ($v) => '£' . number_format((float) $v, 2);

    // "+12.5%" pill (green up / red down). Built only from a number, so it is safe to print raw.
    $pill = function ($pct) {
        if ($pct === null) {
            return '';
        }
        $up = $pct >= 0;
        $c  = $up ? 'success' : 'danger';

        return '<p class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-' . $c . ' text-' . $c . ' bg-opacity-10">'
             . '<span class="material-icons-outlined fs-6">' . ($up ? 'arrow_upward' : 'arrow_downward') . '</span>'
             . abs($pct) . '%</p>';
    };

    $statusMeta = [
        'pending'   => ['icon' => 'schedule',             'grd' => 'bg-grd-warning', 'badge' => 'secondary'],
        'confirmed' => ['icon' => 'thumb_up',             'grd' => 'bg-grd-info',    'badge' => 'info'],
        'preparing' => ['icon' => 'restaurant',           'grd' => 'bg-grd-primary', 'badge' => 'primary'],
        'ready'     => ['icon' => 'notifications_active','grd' => 'bg-grd-branding','badge' => 'warning'],
        'completed' => ['icon' => 'check_circle',         'grd' => 'bg-grd-success', 'badge' => 'success'],
        'cancelled' => ['icon' => 'cancel',               'grd' => 'bg-grd-danger',  'badge' => 'danger'],
    ];
    $totalStatusOrders = max($statusCounts->sum(), 1);
    $peityColors = ['#0d6efd', '#fc185a', '#02c27a', '#fd7e14', '#0dcaf0', '#6f42c1'];
  @endphp

  <!--breadcrumb-->
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Dashboard</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Overview</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="text-muted small">Range:</span>
      @foreach ([7 => '7 days', 14 => '14 days', 30 => '30 days'] as $value => $label)
        <a href="{{ route('dashboard', ['range' => $value]) }}" class="btn btn-sm {{ $days === $value ? 'btn-dark' : 'btn-outline-secondary' }}">{{ $label }}</a>
      @endforeach
    </div>
  </div>
  <!--end breadcrumb-->

  <div class="row">

    {{-- ============ WELCOME + TODAY'S SALES ============ --}}
    <div class="col-xxl-8 d-flex align-items-stretch">
      <div class="card w-100 overflow-hidden rounded-4">
        <div class="card-body position-relative p-4">
          <div class="row">
            <div class="col-12 col-sm-7">
              <div class="d-flex align-items-center gap-3 mb-5">
                <img src="{{ asset('assets/images/avatars/01.png') }}" class="rounded-circle bg-grd-info p-1" width="60" height="60" alt="user">
                <div>
                  <p class="mb-0 fw-semibold">Welcome back</p>
                  <h4 class="fw-semibold mb-0 fs-4 mb-0">{{ auth()->user()->name ?? 'Admin' }}!</h4>
                </div>
              </div>
              <div class="d-flex align-items-center gap-5 flex-wrap">
                <div>
                  <h4 class="mb-1 fw-semibold d-flex align-content-center">
                    {{ $money($salesToday) }}
                    @if ($salesChange !== null)
                      <i class="material-icons-outlined fs-5 lh-base {{ $salesChange >= 0 ? 'text-success' : 'text-danger' }}">{{ $salesChange >= 0 ? 'arrow_upward' : 'arrow_downward' }}</i>
                    @endif
                  </h4>
                  <p class="mb-3">Today's Sales
                    @if ($salesChange !== null)<span class="text-muted small">({{ abs($salesChange) }}% vs yesterday)</span>@endif
                  </p>
                  <div class="progress mb-0" style="height:5px;">
                    <div class="progress-bar bg-grd-success" role="progressbar" style="width: {{ min(100, $salesYesterday > 0 ? $salesToday / $salesYesterday * 100 : ($salesToday > 0 ? 100 : 0)) }}%"></div>
                  </div>
                </div>
                <div class="vr"></div>
                <div>
                  <h4 class="mb-1 fw-semibold">{{ number_format($ordersToday) }}</h4>
                  <p class="mb-3">Orders today <span class="text-muted small">({{ number_format($pendingOrders) }} pending)</span></p>
                  <div class="progress mb-0" style="height:5px;">
                    <div class="progress-bar bg-grd-danger" role="progressbar" style="width: {{ $ordersToday ? round($completedToday / $ordersToday * 100) : 0 }}%"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-5">
              <div class="welcome-back-img pt-4">
                <img src="{{ asset('assets/images/gallery/welcome-back-3.png') }}" height="180" alt="">
              </div>
            </div>
          </div><!--end row-->
        </div>
      </div>
    </div>

    {{-- ============ VISITORS TODAY ============ --}}
    <div class="col-xl-6 col-xxl-2 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div>
              <h5 class="mb-0">{{ number_format($visitorsToday) }}</h5>
              <p class="mb-0">Visitors today</p>
            </div>
            <a href="{{ route('visitors.index') }}" class="options" title="Open visitors">
              <span class="material-icons-outlined fs-5">open_in_new</span>
            </a>
          </div>
          <div class="chart-container2">
            <div id="dash-visitors-spark"></div>
          </div>
          <div class="text-center">
            <p class="mb-0 font-12"><span class="text-success me-1">{{ number_format($onlineNow) }}</span> online now</p>
          </div>
        </div>
      </div>
    </div>

    {{-- ============ CUSTOMERS ============ --}}
    <div class="col-xl-6 col-xxl-2 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div>
              <h5 class="mb-0">{{ $customers ? number_format($customers['total']) : '—' }}</h5>
              <p class="mb-0">Customers</p>
            </div>
          </div>
          <div class="chart-container2">
            <div id="dash-customers-spark"></div>
          </div>
          <div class="text-center">
            <p class="mb-0 font-12">
              @if ($customers)
                <span class="text-success me-1">+{{ number_format($customers['period']) }}</span> in {{ $days }} days
              @else
                No customer data
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- ============ REVENUE ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="text-center">
            <h6 class="mb-0">Revenue · last {{ $days }} days</h6>
          </div>
          <div class="mt-4" id="dash-revenue"></div>
          <p class="mb-0 text-muted">Average order value: {{ $money($aov) }}</p>
          <div class="d-flex align-items-center gap-3 mt-3">
            <h2 class="mb-0 text-primary">{{ $money($revenue) }}</h2>
            <div class="align-self-end">{!! $pill($revenueChange) !!}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- ============ DEVICE TYPE ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-start justify-content-between">
              <h5 class="mb-0">Device Type</h5>
              <span class="text-muted small">{{ $days }} days</span>
            </div>

            <div class="position-relative">
              @if ($deviceTotal > 0)
                <div id="dash-devices"></div>
              @else
                <div class="text-center text-muted py-5">No visitors tracked yet.</div>
              @endif
            </div>

            <div class="d-flex flex-column gap-3">
              @foreach ([['desktop', 'desktop_windows', 'text-primary', 'Desktop'], ['tablet', 'tablet_mac', 'text-danger', 'Tablet'], ['mobile', 'phone_android', 'text-success', 'Mobile']] as [$key, $icon, $color, $label])
                <div class="d-flex align-items-center justify-content-between">
                  <p class="mb-0 d-flex align-items-center gap-2 w-25"><span class="material-icons-outlined fs-6 {{ $color }}">{{ $icon }}</span>{{ $label }}</p>
                  <p class="mb-0">{{ $deviceTotal ? round($devices[$key] / $deviceTotal * 100) : 0 }}% · {{ number_format($devices[$key]) }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ============ SMALL CARDS + TOTAL ORDERS ============ --}}
    <div class="col-xxl-4">
      <div class="row">
        <div class="col-md-6 d-flex align-items-stretch">
          <div class="card w-100 rounded-4">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-1">
                <div>
                  <h5 class="mb-0">{{ number_format($pageViews) }}</h5>
                  <p class="mb-0">Page views</p>
                </div>
              </div>
              <div class="chart-container2">
                <div id="dash-views-spark"></div>
              </div>
              <div class="text-center">
                <p class="mb-0 font-12">last {{ $days }} days</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 d-flex align-items-stretch">
          <div class="card w-100 rounded-4">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between mb-1">
                <div>
                  <h5 class="mb-0">{{ number_format($visitors) }}</h5>
                  <p class="mb-0">Unique visitors</p>
                </div>
              </div>
              <div class="chart-container2">
                <div id="dash-visitors-spark2"></div>
              </div>
              <div class="text-center">
                <p class="mb-0 font-12">
                  @if ($visitorsChange !== null)
                    <span class="{{ $visitorsChange >= 0 ? 'text-success' : 'text-danger' }} me-1">{{ $visitorsChange >= 0 ? '+' : '' }}{{ $visitorsChange }}%</span> vs previous {{ $days }} days
                  @else
                    last {{ $days }} days
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-2">
            <h3 class="mb-0">{{ number_format($orderCount) }}</h3>
            <div class="flex-grow-0">{!! $pill($ordersChange) !!}</div>
          </div>
          <p class="mb-0">Orders · last {{ $days }} days</p>
          <div id="dash-orders"></div>
        </div>
      </div>
    </div>

    {{-- ============ ORDERS BY STATUS ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <h6 class="mb-0 fw-bold">Orders by status</h6>
            <span class="text-muted small">{{ $days }} days</span>
          </div>

          <ul class="list-group list-group-flush">
            @foreach ($statusMeta as $status => $meta)
              @php $count = (int) ($statusCounts[$status] ?? 0); @endphp
              <li class="list-group-item px-0 bg-transparent">
                <div class="d-flex align-items-center gap-3">
                  <div class="wh-42 d-flex align-items-center justify-content-center rounded-3 {{ $meta['grd'] }}">
                    <span class="material-icons-outlined text-white">{{ $meta['icon'] }}</span>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0 text-capitalize">{{ $status }}</h6>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <p class="mb-0">{{ number_format($count) }}</p>
                    <p class="mb-0 fw-bold text-{{ $meta['badge'] }}">{{ round($count / $totalStatusOrders * 100) }}%</p>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>

          <div class="d-flex flex-wrap gap-2 mt-3">
            @foreach (['delivery' => 'info', 'pickup' => 'warning', 'dining' => 'primary'] as $type => $c)
              <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }}-subtle text-capitalize">{{ $type }}: {{ number_format((int) ($typeCounts[$type] ?? 0)) }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ============ VISITORS GROWTH + TOP COUNTRIES ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div id="dash-visitors"></div>
          <div class="d-flex align-items-center gap-3 mt-3">
            <h1 class="mb-0">{{ number_format($visitors) }}</h1>
            <div class="align-self-end">{!! $pill($visitorsChange) !!}</div>
          </div>
          <p class="mb-4">Visitors · last {{ $days }} days</p>

          <div class="d-flex flex-column gap-3">
            @forelse ($topCountries as $c)
              <div>
                <p class="mb-1">{{ \App\Models\Visit::flag($c->country_code) }} {{ $c->country }} <span class="float-end">{{ number_format($c->v) }}</span></p>
                <div class="progress" style="height: 5px;">
                  <div class="progress-bar {{ ['bg-grd-primary', 'bg-grd-warning', 'bg-grd-info', 'bg-grd-success'][$loop->index] ?? 'bg-grd-primary' }}" style="width: {{ $visitors ? min(100, round($c->v / $visitors * 100)) : 0 }}%"></div>
                </div>
              </div>
            @empty
              <p class="text-muted mb-0">No location data yet.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- ============ TRAFFIC SOURCES ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <h5 class="mb-0 fw-bold">Traffic sources</h5>
            <span class="text-muted small">{{ $days }} days</span>
          </div>
          <div class="d-flex flex-column justify-content-between gap-4">
            @foreach ($sources as $i => $s)
              @php $share = $visitors ? min(100, round($s['visitors'] / $visitors * 100)) : 0; @endphp
              <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                  <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;">{{ strtoupper(substr($s['label'], 0, 1)) }}</div>
                  <p class="mb-0 text-truncate" style="max-width:150px;" title="{{ $s['label'] }}">{{ $s['label'] }}</p>
                </div>
                <div><p class="mb-0 fs-6">{{ $share }}%</p></div>
                <div>
                  <p class="mb-0 data-attributes">
                    <span data-peity='{ "fill": ["{{ $peityColors[$i % count($peityColors)] }}", "rgb(255 255 255 / 10%)"], "innerRadius": 14, "radius": 18 }'>{{ min($s['visitors'], max($visitors, 1)) }}/{{ max($visitors, 1) }}</span>
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ============ LATEST CUSTOMERS ============ --}}
    <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-header border-0 p-3 border-bottom">
          <h5 class="mb-0">Latest customers</h5>
        </div>
        <div class="card-body p-0">
          <div class="user-list p-3">
            <div class="d-flex flex-column gap-3">
              @forelse ($customers['latest'] ?? [] as $c)
                @php
                  $name = trim($c->name ?? (($c->first_name ?? '') . ' ' . ($c->last_name ?? '')));
                  $name = $name !== '' ? $name : 'Customer';
                @endphp
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width:45px;height:45px;">{{ strtoupper(mb_substr($name, 0, 1)) }}</div>
                  <div class="flex-grow-1" style="min-width:0;">
                    <h6 class="mb-0 text-truncate">{{ $name }}</h6>
                    <p class="mb-0 text-truncate">{{ $c->email ?? '' }}</p>
                  </div>
                  <span class="text-muted small text-nowrap">{{ $c->created_at?->diffForHumans(null, true, true) }}</span>
                </div>
              @empty
                <div class="text-center text-muted py-5">No customers yet.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ============ RECENT ORDERS ============ --}}
    <div class="col-lg-12 col-xxl-8 d-flex align-items-stretch">
      <div class="card w-100 rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-3">
            <h5 class="mb-0">Recent Orders</h5>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
          </div>
          <div class="table-responsive white-space-nowrap">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Order</th>
                  <th>Customer</th>
                  <th>Type</th>
                  <th>Total</th>
                  <th>Payment</th>
                  <th>Status</th>
                  <th>Placed</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($recentOrders as $order)
                  @php
                    $sc = $statusMeta[$order->status]['badge'] ?? 'secondary';
                    $typeClass = ['delivery' => 'info', 'pickup' => 'warning', 'dining' => 'primary'][$order->order_type] ?? 'secondary';
                    $payStatus = $order->latestPayment->status ?? null;
                    $payClass = ['paid' => 'success', 'pending' => 'secondary', 'failed' => 'danger', 'refunded' => 'warning', 'cancelled' => 'dark'][$payStatus] ?? 'secondary';
                  @endphp
                  <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td>
                      {{ $order->first_name }} {{ $order->last_name }}
                      <br><span class="text-muted small">{{ $order->email }}</span>
                    </td>
                    <td><span class="badge bg-{{ $typeClass }}-subtle text-{{ $typeClass }} border border-{{ $typeClass }}-subtle text-capitalize">{{ $order->order_type }}</span></td>
                    <td class="fw-semibold">{{ $money($order->total) }}</td>
                    <td>
                      @if ($payStatus)
                        <span class="badge bg-{{ $payClass }}-subtle text-{{ $payClass }} border border-{{ $payClass }}-subtle text-capitalize">{{ $payStatus }}</span>
                      @else
                        <span class="text-muted small">—</span>
                      @endif
                    </td>
                    <td><p class="dash-lable mb-0 bg-{{ $sc }} bg-opacity-10 text-{{ $sc }} rounded-2 text-capitalize">{{ $order->status }}</p></td>
                    <td><span class="text-muted small">{{ $order->created_at->format('d M, g:i A') }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center text-muted py-5">No orders yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection

@push('scripts')
<script>
(function () {
  if (typeof ApexCharts === 'undefined') return;

  const data = @json($charts);
  const fore = getComputedStyle(document.body).color;   // follows whichever admin theme is active
  const grid = 'rgba(128,128,128,.2)';
  const money = v => '£' + Number(v).toLocaleString(undefined, { maximumFractionDigits: 0 });

  function draw(sel, opts) {
    const el = document.querySelector(sel);
    if (!el) return;
    opts.chart = Object.assign({ foreColor: fore, toolbar: { show: false }, fontFamily: 'inherit' }, opts.chart || {});
    opts.dataLabels = Object.assign({ enabled: false }, opts.dataLabels || {});
    opts.grid = Object.assign({ borderColor: grid }, opts.grid || {});
    new ApexCharts(el, opts).render();
  }

  function spark(sel, series, color, name) {
    draw(sel, {
      chart: { type: 'area', height: 60, sparkline: { enabled: true } },
      series: [{ name: name, data: series }],
      colors: [color],
      stroke: { width: 2, curve: 'smooth' },
      fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
      xaxis: { categories: data.labels },
      tooltip: { fixed: { enabled: false }, x: { show: true } }
    });
  }

  spark('#dash-visitors-spark',  data.visitors.slice(-7), '#0d6efd', 'Visitors');
  spark('#dash-visitors-spark2', data.visitors,           '#02c27a', 'Visitors');
  spark('#dash-views-spark',     data.views,              '#fd7e14', 'Page views');
  spark('#dash-customers-spark', data.customers,          '#fc185a', 'New customers');

  // Revenue per day
  draw('#dash-revenue', {
    chart: { type: 'area', height: 250 },
    series: [{ name: 'Revenue', data: data.revenue }],
    colors: ['#0d6efd'],
    stroke: { width: 2, curve: 'smooth' },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
    xaxis: { categories: data.labels, tickAmount: 6, labels: { rotate: 0 } },
    yaxis: { labels: { formatter: money } },
    tooltip: { y: { formatter: v => '£' + Number(v).toFixed(2) } }
  });

  // Orders per day
  draw('#dash-orders', {
    chart: { type: 'bar', height: 140, sparkline: { enabled: true } },
    series: [{ name: 'Orders', data: data.orders }],
    colors: ['#0d6efd'],
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
    xaxis: { categories: data.labels }
  });

  // Unique visitors per day
  draw('#dash-visitors', {
    chart: { type: 'area', height: 180 },
    series: [{ name: 'Visitors', data: data.visitors }],
    colors: ['#02c27a'],
    stroke: { width: 2, curve: 'smooth' },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
    xaxis: { categories: data.labels, tickAmount: 6, labels: { rotate: 0 } },
    yaxis: { labels: { formatter: v => Math.round(v) } }
  });

  // Device donut
  draw('#dash-devices', {
    chart: { type: 'donut', height: 250 },
    series: data.devices.values,
    labels: data.devices.labels,
    colors: ['#0d6efd', '#fc185a', '#02c27a'],
    legend: { show: false },
    stroke: { width: 0 },
    plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: {
      show: true, label: 'Visitors', color: fore,
      formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
    } } } } }
  });
})();
</script>
@endpush
