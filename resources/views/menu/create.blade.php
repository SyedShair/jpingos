@extends('layouts.app')

@section('title', 'Add Dish')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Dish</li>
        </ol>
      </nav>
    </div>
  </div>

  <form method="POST" action="{{ route('menu.store') }}">
    @include('menu._form')
    <input type="hidden" name="draft_token" value="{{ $draftToken }}">
  </form>

  @php($galleryMode = 'create')
  @include('menu._gallery-scripts')

@endsection