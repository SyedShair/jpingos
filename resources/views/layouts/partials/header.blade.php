<!--start header-->
<header class="top-header">
  <nav class="navbar navbar-expand align-items-center gap-4">
    <div class="btn-toggle">
      <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
    </div>
    <div class="search-bar flex-grow-1">
      <div class="position-relative">
        <input class="form-control rounded-5 px-5 search-control d-lg-block d-none" type="text" placeholder="Search">
        <span class="material-icons-outlined position-absolute d-lg-block d-none ms-3 translate-middle-y start-0 top-50">search</span>
        <span class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 search-close">close</span>
        <div class="search-popup p-3">
          <div class="card rounded-4 overflow-hidden">
            <div class="card-header d-lg-none">
              <div class="position-relative">
                <input class="form-control rounded-5 px-5 mobile-search-control" type="text" placeholder="Search">
                <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50">search</span>
                <span class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 mobile-search-close">close</span>
               </div>
            </div>
           
          </div>
        </div>
      </div>
    </div>
    <ul class="navbar-nav gap-1 nav-right-links align-items-center">
      <li class="nav-item d-lg-none mobile-search-btn">
        <a class="nav-link" href="javascript:;"><i class="material-icons-outlined">search</i></a>
      </li>
    

   

      {{-- =================================================
           PENDING ORDERS
           Replaces the demo "Notifications" dropdown with the
           restaurant's real pending orders, newest first.
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
            <a href="{{ route('orders.index') }}" class="btn w-100">View All Orders</a>
          </div>
        </div>
      </li>

      {{-- Polls every 20s so a new order shows up without a page reload. Self-contained —
           safe to include once per page even though the header itself is shared across all
           admin pages. --}}
      <script>
      (function () {
        function refreshPendingOrders() {
          fetch('{{ route('admin.pending-orders.poll') }}', { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
            .then(function (data) {
              document.getElementById('pending-orders-list').innerHTML = data.html;
              document.getElementById('pending-orders-count-label').textContent = data.count + ' waiting';

              var badge = document.getElementById('pending-orders-badge');
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

      
      <li class="nav-item dropdown">
        <a href="javascrpt:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
           <img src="{{ asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 border" width="45" height="45" alt="">
        </a>
        <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
          <a class="dropdown-item  gap-2 py-2" href="javascript:;">
            <div class="text-center">
              <img src="{{ asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 shadow mb-3" width="90" height="90"
                alt="">
                @php $user = \Auth::user()->name; @endphp
              <h5 class="user-name mb-0 fw-bold">{{$user}}</h5>
            </div>
          </a>
          <hr class="dropdown-divider">
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
            class="material-icons-outlined">person_outline</i>Profile</a>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
            class="material-icons-outlined">local_bar</i>Setting</a>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
            class="material-icons-outlined">dashboard</i>Dashboard</a>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
            class="material-icons-outlined">account_balance</i>Earning</a>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
              class="material-icons-outlined">cloud_download</i>Downloads</a>
          <hr class="dropdown-divider">
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
          class="material-icons-outlined">power_settings_new</i>Logout</a>
        </div>
      </li>
    </ul>

  </nav>
</header>
<!--end header-->
