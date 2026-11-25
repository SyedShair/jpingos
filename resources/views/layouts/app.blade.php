<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <script>
    // Runs before any CSS loads so the saved theme applies immediately —
    // without this, the page would flash the default Blue theme and then
    // jump to the saved one once the bottom-of-body script runs.
    (function () {
      try {
        var saved = localStorage.getItem('MaxtonTheme');
        if (saved && saved !== 'light-theme') {
          document.documentElement.setAttribute('data-bs-theme', saved);
        } else if (saved === 'light-theme') {
          document.documentElement.removeAttribute('data-bs-theme');
        }
      } catch (e) {
        // localStorage unavailable — falls back to the default Blue theme.
      }
    })();
  </script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Maxton') | Bootstrap 5 Admin Dashboard Template</title>
  <!--favicon-->
  <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png">
  <!-- loader-->
  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>

  <!--plugins-->
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/mm-vertical.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}">
  <!--bootstrap css-->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/main.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/dark-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/blue-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/semi-dark.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/bordered-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/responsive.css') }}" rel="stylesheet">

  @stack('styles')

  @livewireStyles
</head>
{{--
    Paste this block into your admin layout (resources/views/layouts/app.blade.php)
    just before </body>.  No libraries needed — it works with plain JS.

    Use anywhere:   toast('success', 'Saved!');
                    toast('error', 'Something went wrong.');
                    toast('info', 'Heads up.');
--}}
<script>
(function () {
    const css = `
    #toast-stack{position:fixed;top:18px;right:18px;z-index:99999;display:flex;flex-direction:column;gap:10px;width:min(360px,calc(100vw - 36px));pointer-events:none}
    .toast-card{pointer-events:auto;position:relative;overflow:hidden;display:flex;align-items:flex-start;gap:12px;
        background:#fff;color:#1f2937;border-radius:12px;padding:14px 40px 16px 14px;
        box-shadow:0 10px 30px rgba(0,0,0,.18),0 2px 6px rgba(0,0,0,.08);border-left:5px solid var(--tc);
        transform:translateX(120%);opacity:0;transition:transform .35s cubic-bezier(.2,.8,.2,1),opacity .35s ease}
    .toast-card.show{transform:translateX(0);opacity:1}
    .toast-card.hide{transform:translateX(120%);opacity:0}
    .toast-card .t-icon{flex:0 0 auto;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
        background:color-mix(in srgb,var(--tc) 14%,#fff);color:var(--tc)}
    .toast-card .t-icon svg{width:18px;height:18px}
    .toast-card .t-title{font:700 14px/1.3 system-ui,sans-serif;margin:0 0 2px}
    .toast-card .t-msg{font:400 13px/1.45 system-ui,sans-serif;color:#4b5563;margin:0;word-break:break-word}
    .toast-card .t-close{position:absolute;top:8px;right:8px;width:26px;height:26px;border:0;border-radius:6px;background:transparent;
        color:#9ca3af;font-size:18px;line-height:1;cursor:pointer}
    .toast-card .t-close:hover{background:#f3f4f6;color:#374151}
    .toast-card .t-bar{position:absolute;left:0;bottom:0;height:3px;width:100%;background:var(--tc);transform-origin:left;animation:toast-bar linear forwards}
    @keyframes toast-bar{to{transform:scaleX(0)}}
    .toast-card.success{--tc:#1a9c5c}.toast-card.error{--tc:#b22b40}.toast-card.info{--tc:#A78360}
    @media (prefers-reduced-motion:reduce){.toast-card{transition:none}.toast-card .t-bar{animation:none}}
    `;
    const style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    const icons = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>',
        error:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>',
        info:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8h.01M11 12h1v5h1"/></svg>'
    };
    const titles = { success: 'Success', error: 'Something went wrong', info: 'Notice' };

    function stack() {
        let el = document.getElementById('toast-stack');
        if (!el) {
            el = document.createElement('div');
            el.id = 'toast-stack';
            document.body.appendChild(el);
        }
        return el;
    }

    window.toast = function (type, message, duration) {
        type = icons[type] ? type : 'info';
        duration = duration || (type === 'error' ? 7000 : 4500);

        const card = document.createElement('div');
        card.className = 'toast-card ' + type;
        card.setAttribute('role', type === 'error' ? 'alert' : 'status');
        card.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

        const icon = document.createElement('div');
        icon.className = 't-icon';
        icon.innerHTML = icons[type];

        const body = document.createElement('div');
        const t = document.createElement('p');
        t.className = 't-title';
        t.textContent = titles[type];
        const m = document.createElement('p');
        m.className = 't-msg';
        m.textContent = message;          // textContent: safe against HTML in messages
        body.append(t, m);

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 't-close';
        close.setAttribute('aria-label', 'Dismiss');
        close.innerHTML = '&times;';

        const bar = document.createElement('div');
        bar.className = 't-bar';
        bar.style.animationDuration = duration + 'ms';

        card.append(icon, body, close, bar);
        stack().appendChild(card);
        requestAnimationFrame(() => card.classList.add('show'));

        let timer = setTimeout(dismiss, duration);
        function dismiss() {
            clearTimeout(timer);
            card.classList.remove('show');
            card.classList.add('hide');
            setTimeout(() => card.remove(), 400);
        }
        close.addEventListener('click', dismiss);
    };

    /* -----------------------------------------------------------
       Send a receipt to the Star printer and show the result as a toast.
       Usage:  printOrderReceipt(ORDER_ID, buttonElement, optionalWidth)
       Width: 3rd argument, else the #thermal-width-select dropdown,
       else the width saved by the order board, else 80.
    ----------------------------------------------------------- */
    window.printOrderReceipt = async function (orderId, btn, widthOverride) {
        const widthSelect = document.getElementById('thermal-width-select');
        const width = Number(
            widthOverride
            || (widthSelect ? widthSelect.value : null)
            || localStorage.getItem('thermalWidth')
            || 80
        );
        const url = "{{ route('orders.print.escpos', ['order' => '__ID__']) }}".replace('__ID__', orderId);

        const original = btn ? btn.innerHTML : null;
        if (btn) {
            btn.disabled = true;
            // Icon-only buttons (data-icon-only) get a small spinner instead of text
            btn.innerHTML = btn.hasAttribute('data-icon-only')
                ? '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                : 'Printing...';
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ width: width }),
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok || data.ok === false) {
                if (data.detail) console.error('Print error detail:', data.detail);
                throw new Error(data.message || 'Print failed. Please try again.');
            }

            toast('success', data.message || 'Receipt sent to the printer.');
        } catch (e) {
            toast('error', e.message);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }
    };
})();
</script>
<body>

  @include('layouts.partials.header')
  @include('layouts.partials.sidebar')

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      @yield('content')
      {{ $slot ?? '' }}
    </div>

    <!--end main wrapper-->

    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

    @include('layouts.partials.cart-offcanvas')

    @include('layouts.partials.theme-switcher')
  </main>

  <!--bootstrap js-->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

  <!--plugins-->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/plugins/metismenu/metisMenu.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>
  <script>
    $(".data-attributes span").peity("donut")
  </script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard1.js') }}"></script>
  <script>
    new PerfectScrollbar(".user-list")
  </script>

  @stack('scripts')

  @include('layouts.partials.toast-container')

  @livewireScripts

</body>

</html>