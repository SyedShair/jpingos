  <!-- Category Banners — pulls from the separate CategoryMedia CRUD via
       $main->media, not from any field on Category itself. Replace the
       equivalent section in storefront/home.blade.php with this. -->
  <div class="section section-margin">
    <div class="container">
      <div class="row mb-n6">
        @forelse ($categories->take(3) as $index => $main)
          <div class="col-lg-4 col-md-6 col-12 mb-6">
            <div class="banner">
              <div class="banner-image">
                <a href="#{{ $main->slug }}">
                  <img src="{{ $main->media?->image_url ?? asset('storefront/assets/images/banner/banner-'.($index + 1).'.jpg') }}"
                    alt="{{ $main->name }}">
                </a>
              </div>
              <div class="info">
                <div class="small-banner-content">
                  <h4 class="sub-title">{{ $main->name }}</h4>
                  <h3 class="title">{{ $main->description ?: 'Explore the menu' }}</h3>
                  <div class="d-flex gap-2 flex-wrap">
                    <a href="#{{ $main->slug }}" class="btn btn-dark btn-sm">View</a>
                    @if ($main->media?->hasPdfMenu())
                      <a href="{{ $main->media->pdf_menu_url }}" target="_blank" class="btn btn-outline-dark btn-sm">
                        <i class="fa fa-file-pdf-o"></i> Download Menu
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted">Add some categories from the dashboard to feature them here.</div>
        @endforelse
      </div>
    </div>
  </div>
