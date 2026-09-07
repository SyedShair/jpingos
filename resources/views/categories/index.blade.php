@extends('layouts.app')

@section('title', 'Categories')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item active" aria-current="page">Categories</li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto">
      <a href="{{ route('categories.create') }}" class="btn btn-grd-primary">
        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">add</i>Add Category
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Order</th>
              <th>Category</th>
              <th>SubCategory</th>
              <th>Description</th>
              <th>Dishes</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($categories as $category)
              <tr>
                <td>{{ $category->sort_order }}</td>

                {{-- Category: the main/parent category. For a main category
                     row, that's itself; for a subcategory row, that's its parent. --}}
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if ($category->parent_id)
                      @if ($category->parent?->icon)
                        <span class="material-icons-outlined text-primary">{{ $category->parent->icon }}</span>
                      @endif
                      <span class="fw-semibold">{{ $category->parent->name ?? '—' }}</span>
                    @else
                      @if ($category->icon)
                        <span class="material-icons-outlined text-primary">{{ $category->icon }}</span>
                      @endif
                      <span class="fw-semibold">{{ $category->name }}</span>
                    @endif
                  </div>
                </td>

                {{-- SubCategory: only filled in for child rows. --}}
                <td>
                  @if ($category->parent_id)
                    <div class="d-flex align-items-center gap-2">
                      <i class="material-icons-outlined text-muted" style="font-size:16px;">subdirectory_arrow_right</i>
                      @if ($category->icon)
                        <span class="material-icons-outlined text-primary">{{ $category->icon }}</span>
                      @endif
                      <span>{{ $category->name }}</span>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td>{{ $category->description ?: '—' }}</td>
                <td>
                  <a href="{{ route('menu.index', ['category' => $category->id]) }}">
                    {{ $category->menu_items_count }} {{ Str::plural('dish', $category->menu_items_count) }}
                  </a>
                </td>
                <td>
                  @if ($category->is_active)
                    <p class="dash-lable mb-0 bg-success bg-opacity-10 text-success rounded-2">Active</p>
                  @else
                    <p class="dash-lable mb-0 bg-secondary bg-opacity-10 text-secondary rounded-2">Hidden</p>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                    <i class="material-icons-outlined fs-6 align-middle">edit</i>
                  </a>
                  <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete {{ $category->name }}? Dishes in this category will become uncategorized, not deleted.')">
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
                <td colspan="7" class="text-center py-4">
                  No categories yet. <a href="{{ route('categories.create') }}">Add your first one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $categories->links() }}
      </div>
    </div>
  </div>

@endsection