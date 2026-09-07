@extends('layouts.app')

@section('title', 'Menu')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">All Dishes</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
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

      <form method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-auto">
          <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach ($categories->groupBy(fn ($c) => $c->parent->name ?? $c->name) as $groupName => $group)
              <optgroup label="{{ $groupName }}">
                @foreach ($group as $category)
                  <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                    {{ $category->name }}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
        </div>
        <div class="col-auto flex-grow-1">
          <div class="position-relative">
            <input type="text" name="search" value="{{ request('search') }}"
              class="form-control rounded-5 px-4" placeholder="Search dishes...">
          </div>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-outline-primary">Filter</button>
          @if (request()->hasAny(['category', 'search']))
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Dish</th>
              <th>Category</th>
              <th>Price</th>
              <th>Spice</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $item)
              <tr>
                <td>
                  <a href="{{ route('menu.show', $item) }}" class="d-flex align-items-center gap-3 text-decoration-none text-body">
                    <img src="{{ $item->image_url }}" class="rounded-circle" width="50" height="50"
                      style="object-fit:cover;" alt="{{ $item->name }}">
                    <div>
                      <p class="mb-0 fw-semibold">{{ $item->name }}</p>
                      @if ($item->is_featured)
                        <span class="badge bg-warning bg-opacity-10 text-warning">Featured</span>
                      @endif
                    </div>
                  </a>
                </td>
                <td>
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
                <td>
                  @if ($item->is_on_sale)
                    <span class="text-danger fw-semibold">${{ number_format($item->discount_price, 2) }}</span>
                    <span class="text-muted text-decoration-line-through ms-1">${{ number_format($item->price, 2) }}</span>
                  @else
                    ${{ number_format($item->price, 2) }}
                  @endif
                </td>
                <td>{{ ucfirst($item->spice_level) }}</td>
                <td>
                  @if ($item->is_available)
                    <p class="dash-lable mb-0 bg-success bg-opacity-10 text-success rounded-2">Available</p>
                  @else
                    <p class="dash-lable mb-0 bg-danger bg-opacity-10 text-danger rounded-2">86'd</p>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('menu.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="material-icons-outlined fs-6 align-middle">visibility</i>
                  </a>
                  <a href="{{ route('menu.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                    <i class="material-icons-outlined fs-6 align-middle">edit</i>
                  </a>
                  <form action="{{ route('menu.destroy', $item) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Remove {{ $item->name }} from the menu?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="material-icons-outlined fs-6 align-middle">delete</i>
                    </button>
                  </form>
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

      <div class="mt-3">
        {{ $items->links() }}
      </div>

    </div>
  </div>

@endsection