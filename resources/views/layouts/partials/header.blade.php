<!--start header-->
<header class="top-header">
  <nav class="navbar navbar-expand align-items-center gap-4">

    {{-- Sidebar toggle --}}
    <div class="btn-toggle">
      <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
    </div>

    {{-- Spacer: pushes the icons to the right (replaces the demo search bar) --}}
    <div class="flex-grow-1"></div>

    <ul class="navbar-nav gap-1 nav-right-links align-items-center">

      {{-- =================================================
           PENDING ORDERS
           The restaurant's real pending orders, newest first.
           Refreshes itself every 20s so new orders show up
           without a full page reload.
      ================================================== --}}
      <li class="nav-item dropdown" id="pending-orders-dropdown">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" data-bs-auto-close="outside"
          data-bs-toggle="dropdown" href="javascript:;"><i class="material-icons-outlined">notifications</i>
          <span class="badge-notify" id="pending-orders-badge" @if ($pendingOrdersCount == 0) style="display:none;" @endif>{{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}</span>
        </a>
        <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
          <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="notiy-title mb-0">Pending Orders</h5>
            <span class="text-muted small" id="pending-orders-count-label">{{ $pendingOrdersCount }} waiting</span>
          </div>
          <div class="notify-list" id="pending-orders-list">
            @include('layouts.partials.pending-orders-list')
          </div>
          <div class="card-footer text-center bg-transparent">
            <a href="{{ route('orders.accept') }}" class="btn w-100">Open Order Board</a>
            <a href="{{ route('orders.index') }}" class="btn w-100">View All Orders</a>
          </div>
        </div>
      </li>

      {{-- User menu --}}
      <li class="nav-item dropdown">
        <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
           <img src="{{ asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 border" width="45" height="45" alt="">
        </a>
        <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
          <div class="dropdown-item gap-2 py-2">
            <div class="text-center">
              <img src="{{ asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 shadow mb-3" width="90" height="90" alt="">
              <h5 class="user-name mb-0 fw-bold">{{ auth()->user()->name }}</h5>
            </div>
          </div>
          <hr class="dropdown-divider">
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('dashboard') }}">
            <i class="material-icons-outlined">dashboard</i>Dashboard
          </a>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('settings.edit') }}">
            <i class="material-icons-outlined">settings</i>Settings
          </a>
          <hr class="dropdown-divider">

          {{-- Logout is a POST route, so it needs a form --}}
          <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
              <i class="material-icons-outlined">power_settings_new</i>Logout
            </button>
          </form>
        </div>
      </li>

    </ul>
  </nav>

  {{-- Empty, hidden placeholders. The template's main.js starts a scrollbar on each of
       these class names at page load. If an element is missing, that line throws an
       error and every script after it stops — including the sidebar toggle. --}}
  <div class="search-content d-none"></div>
  <div class="mega-menu-widgets d-none"></div>
  <div class="dropdown-apps d-none"></div>
</header>
<!--end header-->

{{-- Polls every 20s so a new order shows up without a page reload.
     Self-contained: include the header once per page. --}}
<script>
(function () {
  var list  = document.getElementById('pending-orders-list');
  var label = document.getElementById('pending-orders-count-label');
  var badge = document.getElementById('pending-orders-badge');

  if (!list || !label || !badge) { return; }

  function refreshPendingOrders() {
    fetch('{{ route('admin.pending-orders.poll') }}', { headers: { 'Accept': 'application/json' } })
      .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
      .then(function (data) {
        list.innerHTML = data.html;
        label.textContent = data.count + ' waiting';

        if (data.count > 0) {
          badge.style.display = '';
          badge.textContent = data.count > 99 ? '99+' : data.count;
        } else {
          badge.style.display = 'none';
        }
      })
      .catch(function () { /* next poll will try again — never break the page over this */ });
  }

  setInterval(refreshPendingOrders, 20000);
})();
</script>