{{-- Drop this in place of the old static "Logout" <a> tag inside
     resources/views/layouts/partials/header.blade.php,
     inside the user dropdown menu. --}}

<form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 border-0 bg-transparent w-100 text-start">
    <i class="material-icons-outlined">power_settings_new</i>Logout
  </button>
</form>
