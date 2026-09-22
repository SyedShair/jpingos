<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <script>
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
  <title>@yield('title', 'Accept Orders') | Maxton</title>

  <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png">

  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>

  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/main.css') }}" rel="stylesheet">

  {{-- No header/sidebar partials, no MetisMenu/dashboard/chart JS on
       purpose — this is a deliberately bare canvas so the kiosk screen
       can take the full viewport instead of sitting inside the normal
       admin chrome. --}}

  @stack('styles')

  @livewireStyles
</head>

<body>

  @yield('content')
  {{ $slot ?? '' }}

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

  @stack('scripts')

  @livewireScripts

</body>

</html>