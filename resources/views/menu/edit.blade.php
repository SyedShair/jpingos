@extends('layouts.app')

@section('title', 'Edit Dish')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit Dish</li>
        </ol>
      </nav>
    </div>
  </div>

  <form method="POST" action="{{ route('menu.update', $item) }}">
    @method('PUT')
    @include('menu._form')
  </form>

  @php($galleryMode = 'edit')
  @include('menu._gallery-scripts')

@endsection