@extends('layouts.app')

@section('title', 'Menu')

@section('content')

  {{-- Header: breadcrumb is hidden on phones, but the buttons must stay visible --}}
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center">
      <div class="breadcrumb-title pe-3">Menu</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">All Dishes</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="ms-sm-auto d-flex flex-wrap gap-2">
      <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">category</i>Manage Categories
      </a>
      <a href="{{ route('menu.create') }}" class="btn btn-grd-primary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">add</i>Add Dish
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card rounded-4">
    <div class="card-body">

      {{-- Filters: stack on phones, sit in one row from tablet up --}}
      <form method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-12 col-md-auto">
          <select name="category" id="categoryFilter" class="form-select">
            <option value="">All Categories</option>
            @foreach ($categories->whereNull('parent_id') as $parent)
              @if ($parent->children->isNotEmpty())
                <optgroup label="{{ $parent->name }}">
                  {{-- Picking the group itself shows every dish in all of its sub-categories --}}
                  <option value="{{ $parent->id }}" @selected(request('category') == $parent->id)>
                    All {{ $parent->name }}
                  </option>
                  @foreach ($parent->children as $child)
                    <option value="{{ $child->id }}" @selected(request('category') == $child->id)>
                      {{ $child->name }}@unless ($child->is_active ?? true) (inactive)@endunless
                    </option>
                  @endforeach
                </optgroup>
              @else
                <option value="{{ $parent->id }}" @selected(request('category') == $parent->id)>
                  {{ $parent->name }}@unless ($parent->is_active ?? true) (inactive)@endunless
                </option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md">
          <input type="text" name="search" value="{{ request('search') }}"
            class="form-control rounded-5 px-4" placeholder="Search dishes...">
        </div>
        <div class="col-12 col-md-auto d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary flex-fill">Filter</button>
          @if (request()->hasAny(['category', 'search']))
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
          @endif
        </div>
      </form>

      {{-- The wrapper scrolls sideways on very small screens. (The extra
           "table-responsive" class that used to be on the <table> is removed:
           it nested a second scroll box inside the first.) --}}
      <div class="table-responsive">
        <table class="table align-middle table-hover table-striped mb-0 table-bordered">
          <thead>
            <tr>
              <th>Dish</th>
              <th class="d-none d-lg-table-cell">Category</th>
              <th>Price</th>
              <th class="d-none d-md-table-cell">Spice</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $item)
              <tr>
                <td>
                  <a href="{{ route('menu.show', $item) }}" class="d-flex align-items-center gap-3 text-decoration-none text-body">
                    <img src="{{ $item->image_url }}" class="rounded-circle flex-shrink-0" width="44" height="44"
                      style="object-fit:cover;" alt="{{ $item->name }}">
                    <div>
                      <p class="mb-0 fw-semibold">{{ $item->name }}</p>

                      {{-- On phones/tablets the Category and Spice columns are hidden,
                           so show them here under the name instead --}}
                      <div class="d-lg-none text-muted small">
                        @if ($item->category)
                          {{ $item->category->name }}
                        @else
                          <span class="fst-italic">Uncategorized</span>
                        @endif
                        <span class="d-md-none">· {{ ucfirst($item->spice_level) }}</span>
                      </div>

                      @if ($item->is_featured)
                        <span class="badge bg-warning bg-opacity-10 text-warning">Featured</span>
                      @endif
                    </div>
                  </a>
                </td>
                <td class="d-none d-lg-table-cell">
                  @if ($item->category)
                    @if ($item->category->parent)
                      <span class="text-muted small">{{ $item->category->parent->name }} »</span>
                      {{ $item->category->name }}
                    @else
                      {{ $item->category->name }}
                    @endif
                  @else
                    <span class="text-muted fst-italic">Uncategorized</span>
                  @endif
                </td>
                <td class="text-nowrap">
                  @if ($item->is_on_sale)
                    <span class="text-danger fw-semibold">£{{ number_format($item->discount_price, 2) }}</span>
                    <span class="text-muted text-decoration-line-through ms-1">£{{ number_format($item->price, 2) }}</span>
                  @else
                    £{{ number_format($item->price, 2) }}
                  @endif
                </td>
                <td class="d-none d-md-table-cell">{{ ucfirst($item->spice_level) }}</td>
                <td class="text-nowrap">
                  @if ($item->is_available)
                    <p class="dash-lable mb-0 bg-success bg-opacity-10 text-success rounded-2">Available</p>
                  @else
                    <p class="dash-lable mb-0 bg-danger bg-opacity-10 text-danger rounded-2">86'd</p>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  <div class="d-inline-flex gap-1">
                    <a href="{{ route('menu.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="View">
                      <i class="material-icons-outlined fs-6 align-middle">visibility</i>
                    </a>
                    <a href="{{ route('menu.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                      <i class="material-icons-outlined fs-6 align-middle">edit</i>
                    </a>
                    <form action="{{ route('menu.destroy', $item) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Remove {{ $item->name }} from the menu?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="material-icons-outlined fs-6 align-middle">delete</i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  No dishes yet.
                  <a href="{{ route('menu.create') }}">Add your first one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Bootstrap 5 pagination (the default view is built for Tailwind, which is
           why it showed a giant arrow). withQueryString() keeps the category and
           search filters when you go to the next page. --}}
      @if ($items->hasPages())
        <div class="mt-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
          <div class="text-muted small">
            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} dishes
          </div>
          <div class="overflow-auto">
            {{ $items->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
          </div>
        </div>
      @endif

    </div>
  </div>

@endsection

@push('styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    /* Keep the Select2 box a sensible width inside the filter row */
    @media (min-width: 768px) {
      #categoryFilter + .select2-container { min-width: 260px; }
    }

    /* Show the whole category list: Select2 caps the list at ~200px and some
       admin themes make it shorter still, so a long list looked cut off. */
    .select2-container--bootstrap-5 .select2-results > .select2-results__options {
      max-height: 60vh !important;
      overflow-y: auto;
    }
    .select2-container--bootstrap-5 .select2-results__option,
    .select2-container--bootstrap-5 .select2-results__group {
      white-space: normal;      /* long names wrap instead of being clipped */
    }
    .select2-container { z-index: 1056; }
    .select2-dropdown   { z-index: 1057; }
  </style>
@endpush

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
  <script>
    $(function () {
      var $category = $('#categoryFilter');

      $category.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'All Categories',
        allowClear: true,
        dropdownAutoWidth: true,   // widen the list to fit the longest name
        minimumResultsForSearch: 0 // always show the search box
      });

      // Filter as soon as a category is picked, or cleared with the small x
      $category.on('select2:select select2:clear', function () {
        this.form.submit();
      });
    });
  </script>
@endpush