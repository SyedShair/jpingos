<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Restaurant Admin</title>
  <!--favicon-->
  <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png">
  <!-- loader-->
  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>

  <!--plugins-->
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/mm-vertical.css') }}">
  <!--bootstrap css-->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/main.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/responsive.css') }}" rel="stylesheet">

  <style>
    :root {
      --rc-wine: #6B1118;
      --rc-wine-deep: #4d0b10;
      --rc-tan: #A78360;
      --rc-tan-soft: #f4ece3;
      --rc-ink: #2b1a14;
    }

    body {
      font-family: 'Noto Sans', sans-serif;
      background: var(--rc-tan-soft);
      color: var(--rc-ink);
      min-height: 100vh;
    }

    .login-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1.05fr 1fr;
    }

    /* Left: wine panel */
    .login-brand {
      position: relative;
      background-color: var(--rc-wine);
      background-image:
        radial-gradient(circle at 1px 1px, rgba(167, 131, 96, .28) 1px, transparent 0);
      background-size: 22px 22px;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 4rem;
      overflow: hidden;
    }

    .login-brand::before {
      content: "";
      position: absolute;
      inset: 1.5rem;
      border: 1px solid var(--rc-tan);
      pointer-events: none;
    }

    .login-brand::after {
      content: "";
      position: absolute;
      inset: 2rem;
      border: 1px solid rgba(167, 131, 96, .4);
      pointer-events: none;
    }

    .login-brand h1 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 700;
      font-size: clamp(2.6rem, 5vw, 4.2rem);
      line-height: 1.05;
      margin: 0 0 1rem;
      color: #fff;
      max-width: 11ch;
    }

    .login-brand p {
      color: var(--rc-tan);
      font-size: 1.05rem;
      max-width: 34ch;
      margin: 0;
    }

    /* Right: form panel */
    .login-form-side {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.5rem;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
    }

    .login-card .logo {
      margin-bottom: 2rem;
    }

    .login-card h2 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 700;
      font-size: 2.2rem;
      color: var(--rc-wine);
      margin-bottom: .25rem;
    }

    .login-card .sub {
      color: #6e5a4c;
      margin-bottom: 1.75rem;
    }

    .login-card .form-label {
      font-weight: 500;
      color: var(--rc-ink);
    }

    .login-card .form-control {
      background: #fff;
      border: 1px solid #d9c8b6;
      border-radius: 6px;
      padding: .7rem .9rem;
      color: var(--rc-ink);
    }

    .login-card .form-control:focus {
      border-color: var(--rc-wine);
      box-shadow: 0 0 0 .2rem rgba(107, 17, 24, .15);
    }

    .login-card .input-group-text {
      background: #fff;
      border: 1px solid #d9c8b6;
      border-left: 0;
      color: var(--rc-tan);
    }

    .login-card .input-group .form-control {
      border-right: 0;
    }

    .login-card .form-check-input:checked {
      background-color: var(--rc-wine);
      border-color: var(--rc-wine);
    }

    .login-card .form-check-input:focus {
      border-color: var(--rc-tan);
      box-shadow: 0 0 0 .2rem rgba(167, 131, 96, .3);
    }

    .login-card a {
      color: var(--rc-wine);
      text-decoration: none;
      font-weight: 500;
    }

    .login-card a:hover {
      color: var(--rc-tan);
      text-decoration: underline;
    }

    .btn-wine {
      background: var(--rc-wine);
      border: 1px solid var(--rc-wine);
      color: #fff;
      padding: .75rem 1rem;
      border-radius: 6px;
      font-weight: 600;
      letter-spacing: .02em;
      transition: background .15s ease, border-color .15s ease;
    }

    .btn-wine:hover,
    .btn-wine:focus-visible {
      background: var(--rc-wine-deep);
      border-color: var(--rc-wine-deep);
      color: #fff;
    }

    .btn-wine:focus-visible {
      outline: 3px solid var(--rc-tan);
      outline-offset: 2px;
    }

    .alert-success {
      background: #f1e6d8;
      border: 1px solid var(--rc-tan);
      color: var(--rc-wine);
    }

    @media (max-width: 991.98px) {
      .login-shell {
        grid-template-columns: 1fr;
      }

      .login-brand {
        min-height: 220px;
        padding: 2.5rem 2rem;
      }

      .login-brand h1 {
        font-size: 2.2rem;
        max-width: none;
      }
    }
  </style>
</head>

<body>

  <!--authentication-->
  <div class="login-shell">

    <aside class="login-brand">
      <h1>Good food starts at the counter</h1>
      <p>Sign in to manage orders, menu items and daily service.</p>
    </aside>

    <main class="login-form-side">
      <div class="login-card">
        <img src="{{ asset('assets/images/logo1.png') }}" class="logo" width="145" alt="">
        <h2>Welcome back</h2>
        <p class="sub">Sign in to your account to continue.</p>

        @if (session('status'))
          <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif

        <form class="row g-3" method="POST" action="{{ route('login.store') }}">
          @csrf

          <div class="col-12">
            <label for="inputEmailAddress" class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
              class="form-control @error('email') is-invalid @enderror"
              id="inputEmailAddress" placeholder="name@restaurant.com" required autofocus>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="inputChoosePassword" class="form-label">Password</label>
            <div class="input-group" id="show_hide_password">
              <input type="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                id="inputChoosePassword" placeholder="Enter password" required>
              <a href="javascript:;" class="input-group-text" aria-label="Show or hide password">
                <i class="bi bi-eye-slash-fill"></i>
              </a>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="col-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="remember" id="flexSwitchCheckChecked">
              <label class="form-check-label" for="flexSwitchCheckChecked">Remember me</label>
            </div>
          </div>
        

          <div class="col-12">
            <div class="d-grid">
              <button type="submit" class="btn btn-wine">Log in</button>
            </div>
          </div>

          
        </form>
      </div>
    </main>

  </div>
  <!--authentication-->

  <!--plugins-->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

  <script>
    $(document).ready(function () {
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        var input = $('#show_hide_password input');
        var icon = $('#show_hide_password i');
        if (input.attr("type") === "text") {
          input.attr('type', 'password');
          icon.addClass("bi-eye-slash-fill").removeClass("bi-eye-fill");
        } else {
          input.attr('type', 'text');
          icon.removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
        }
      });
    });
  </script>

</body>

</html>