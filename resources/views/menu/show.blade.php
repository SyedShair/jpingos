@extends('layouts.app')

@section('title', $item->name)

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $item->name }}</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
      <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">arrow_back</i>Back to Menu
      </a>
      <a href="{{ route('menu.edit', $item) }}" class="btn btn-outline-primary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">edit</i>Edit
      </a>
      <form action="{{ route('menu.destroy', $item) }}" method="POST"
        onsubmit="return confirm('Permanently delete {{ $item->name }}? This also removes all its photos.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">
          <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">delete</i>Delete
        </button>
      </form>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">

    {{-- Gallery --}}
    <div class="col-lg-7">
      <div class="card rounded-4 mb-4">
        <div class="card-body">
          <div class="rounded-4 overflow-hidden mb-3" style="aspect-ratio:4/3; background:#f2f2f2;">
            <img id="mainImage" src="{{ $item->image_url }}" alt="{{ $item->name }}"
              class="w-100 h-100" style="object-fit:cover;">
          </div>

          @if ($item->images->count() > 1)
            <div class="d-flex flex-wrap gap-2">
              @foreach ($item->images as $image)
                <button type="button" class="thumb-btn border-0 p-0 rounded-3 overflow-hidden"
                  style="width:72px;height:72px;{{ $image->is_primary ? 'outline:2px solid var(--bs-primary);' : '' }}"
                  onclick="document.getElementById('mainImage').src = '{{ $image->url }}'">
                  <img src="{{ $image->url }}" class="w-100 h-100" style="object-fit:cover;" alt="">
                </button>
              @endforeach
            </div>
          @elseif ($item->images->isEmpty())
            <p class="text-muted mb-0 small">No photos uploaded yet. <a href="{{ route('menu.edit', $item) }}">Add some</a>.</p>
          @endif
        </div>
      </div>
    </div>

    {{-- Details --}}
    <div class="col-lg-5">

      <div class="card rounded-4 mb-4">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                @if ($item->category)
                  @if ($item->category->parent)
                    {{ $item->category->parent->name }} » {{ $item->category->name }}
                  @else
                    {{ $item->category->name }}
                  @endif
                @else
                  Uncategorized
                @endif
              </span>
              <h4 class="fw-bold mb-0">{{ $item->name }}</h4>
            </div>
            @if ($item->is_featured)
              <span class="badge bg-warning bg-opacity-10 text-warning">★ Featured</span>
            @endif
          </div>

          <div class="d-flex align-items-baseline gap-2 mb-3">
            @if ($item->is_on_sale)
              <h3 class="text-danger fw-bold mb-0">${{ number_format($item->discount_price, 2) }}</h3>
              <span class="text-muted text-decoration-line-through fs-5">${{ number_format($item->price, 2) }}</span>
            @else
              <h3 class="fw-bold mb-0">${{ number_format($item->price, 2) }}</h3>
            @endif
          </div>

          @if ($item->description)
            <p class="mb-0">{{ $item->description }}</p>
          @endif
        </div>
      </div>

      <div class="card rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Status</h6>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="toggleAvailable"
              @checked($item->is_available)
              data-url="{{ route('menu.toggle-availability', $item) }}">
            <label class="form-check-label" for="toggleAvailable">Available on menu</label>
          </div>

          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="toggleFeatured"
              @checked($item->is_featured)
              data-url="{{ route('menu.toggle-featured', $item) }}">
            <label class="form-check-label" for="toggleFeatured">Feature this dish</label>
          </div>
        </div>
      </div>

      <div class="card rounded-4 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Details</h6>

          <div class="row g-3">
            <div class="col-6">
              <p class="mb-1 text-muted small">Prep Time</p>
              <p class="mb-0 fw-semibold">
                {{ $item->prep_time_minutes ? $item->prep_time_minutes.' min' : '—' }}
              </p>
            </div>
            <div class="col-6">
              <p class="mb-1 text-muted small">Spice Level</p>
              <p class="mb-0 fw-semibold">{{ ucfirst($item->spice_level) }}</p>
            </div>
          </div>

          @if ($item->is_vegetarian || $item->is_vegan || $item->is_gluten_free)
            <hr>
            <p class="mb-2 text-muted small">Dietary</p>
            <div class="d-flex flex-wrap gap-2">
              @if ($item->is_vegetarian)
                <span class="badge bg-success bg-opacity-10 text-success">Vegetarian</span>
              @endif
              @if ($item->is_vegan)
                <span class="badge bg-success bg-opacity-10 text-success">Vegan</span>
              @endif
              @if ($item->is_gluten_free)
                <span class="badge bg-info bg-opacity-10 text-info">Gluten-Free</span>
              @endif
            </div>
          @endif

          @if ($item->ingredients)
            <hr>
            <p class="mb-1 text-muted small">Ingredients</p>
            <p class="mb-0">{{ $item->ingredients }}</p>
          @endif
        </div>
      </div>

      @if ($item->optionGroups->isNotEmpty())
        <div class="card rounded-4 mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Customizations</h6>
            @foreach ($item->optionGroups as $group)
              <div class="mb-3">
                <p class="mb-1 fw-semibold">
                  {{ $group->name }}
                  <span class="text-muted small">
                    ({{ $group->selection_type === 'single' ? 'pick one' : 'pick multiple' }}{{ $group->min_select > 0 ? ', required' : '' }})
                  </span>
                </p>
                <div class="d-flex flex-wrap gap-2">
                  @foreach ($group->values as $value)
                    <span class="badge bg-light text-dark border">
                      {{ $value->name }}
                      @if ($value->price_delta > 0)
                        <span class="text-success">+${{ number_format($value->price_delta, 2) }}</span>
                      @endif
                    </span>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <div class="card rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Meta</h6>
          <div class="row g-2 small text-muted">
            <div class="col-6">Slug</div>
            <div class="col-6 text-end font-monospace">{{ $item->slug }}</div>
            <div class="col-6">Added</div>
            <div class="col-6 text-end">{{ $item->created_at->format('M j, Y') }}</div>
            <div class="col-6">Last updated</div>
            <div class="col-6 text-end">{{ $item->updated_at->diffForHumans() }}</div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection

@push('scripts')
  <script>
    (function () {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

      function wireToggle(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('change', function () {
          fetch(el.dataset.url, {
            method: 'PATCH',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
          }).catch(() => {
            el.checked = !el.checked;
            alert('Could not update — please try again.');
          });
        });
      }

      wireToggle('toggleAvailable');
      wireToggle('toggleFeatured');
    })();
  </script>
@endpush