@extends('layouts.app')

@section('title', 'Add Category')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Menu</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Category</li>
        </ol>
      </nav>
    </div>
  </div>

  <form method="POST" action="{{ route('categories.store') }}">
    @include('categories._form')
  </form>

@endsection
