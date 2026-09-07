<!--start sidebar-->
<aside class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div class="logo-icon">
      <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-img" alt="">
    </div>
    <div class="logo-name flex-grow-1">
      <h5 class="mb-0">{{ config('app.name', 'Restaurant') }}</h5>
    </div>
    <div class="sidebar-close">
      <span class="material-icons-outlined">close</span>
    </div>
  </div>
  <div class="sidebar-nav">
      <ul class="metismenu" id="sidenav">

        <li>
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
            <div class="parent-icon"><i class="material-icons-outlined">home</i></div>
            <div class="menu-title">Dashboard</div>
          </a>
        </li>

        <li class="menu-label">Menu Management</li>

        <li>
          <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'mm-active' : '' }}">
            <div class="parent-icon"><i class="material-icons-outlined">category</i></div>
            <div class="menu-title">Categories</div>
          </a>
        </li>
        <li>
  <a href="{{ route('category-media.index') }}" class="{{ request()->routeIs('category-media.*') ? 'mm-active' : '' }}">
    <div class="parent-icon"><i class="material-icons-outlined">perm_media</i></div>
    <div class="menu-title">Category Media</div>
  </a>
</li>

        <li>
          <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'mm-active' : '' }}">
            <div class="parent-icon"><i class="material-icons-outlined">restaurant_menu</i></div>
            <div class="menu-title">Menu (Dishes)</div>
          </a>
        </li>

        <li>
          <a href="{{ route('deals.index') }}" class="{{ request()->routeIs('deals.*') ? 'mm-active' : '' }}">
            <div class="parent-icon"><i class="material-icons-outlined">local_offer</i></div>
            <div class="menu-title">Deals &amp; Offers</div>
          </a>
        </li>
<li>
  <a href="{{ route('sliders.index') }}" class="{{ request()->routeIs('sliders.*') ? 'mm-active' : '' }}">
    <div class="parent-icon"><i class="material-icons-outlined">view_carousel</i></div>
    <div class="menu-title">Hero Slider</div>
  </a>
</li>
       </ul>
  </div>
</aside>
<!--end sidebar-->
