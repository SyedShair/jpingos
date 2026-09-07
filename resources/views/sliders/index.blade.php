@extends('layouts.app')

@section('title', 'Hero Slider')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Homepage</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Hero Slider</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto">
      <a href="{{ route('sliders.create') }}" class="btn btn-grd-primary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">add</i>Add Slide
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card rounded-4">
    <div class="card-body">
      <p class="text-muted small mb-3">Drag rows to reorder — the order here matches the order slides play on the homepage.</p>

      <div id="sliders-list">
        @forelse ($sliders as $slider)
          <div class="d-flex align-items-center gap-3 border rounded-4 p-2 mb-2" data-id="{{ $slider->id }}" style="cursor:grab;">
            <span class="material-icons-outlined text-muted">drag_indicator</span>

            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}"
              class="rounded-3" style="width:100px;height:60px;object-fit:cover;">

            <div class="flex-grow-1">
              <p class="mb-0 fw-semibold">{{ $slider->title }}</p>
              @if ($slider->subtitle)
                <p class="mb-0 text-muted small">{{ $slider->subtitle }}</p>
              @endif
            </div>

            @if (! $slider->is_active)
              <span class="badge bg-secondary bg-opacity-10 text-secondary">Hidden</span>
            @endif

            <div class="form-check form-switch mb-0">
              <input class="form-check-input toggle-active" type="checkbox" role="switch"
                data-url="{{ route('sliders.toggle-active', $slider) }}" @checked($slider->is_active)>
            </div>

            <a href="{{ route('sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary">
              <i class="material-icons-outlined fs-6 align-middle">edit</i>
            </a>

            <form action="{{ route('sliders.destroy', $slider) }}" method="POST"
              onsubmit="return confirm('Remove this slide?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="material-icons-outlined fs-6 align-middle">delete</i>
              </button>
            </form>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">
            No slides yet. <a href="{{ route('sliders.create') }}">Add your first one</a>.
          </p>
        @endforelse
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
  <script>
    (function () {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      const list = document.getElementById('sliders-list');

      Sortable.create(list, {
        animation: 150,
        handle: '[data-id]',
        onEnd: function () {
          const order = Array.from(list.children).map(el => el.dataset.id);
          fetch(@json(route('sliders.reorder')), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ order }),
          });
        },
      });

      list.addEventListener('change', function (e) {
        if (!e.target.classList.contains('toggle-active')) return;

        fetch(e.target.dataset.url, {
          method: 'PATCH',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        }).catch(() => {
          e.target.checked = !e.target.checked;
          alert('Could not update — please try again.');
        });
      });
    })();
  </script>
@endpush