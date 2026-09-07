@extends('layouts.app')

@section('title', 'Add Slide')

@section('content')

  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Homepage</div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
          <li class="breadcrumb-item"><a href="{{ route('sliders.index') }}">Hero Slider</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Slide</li>
        </ol>
      </nav>
    </div>
  </div>

  <form method="POST" action="{{ route('sliders.store') }}" enctype="multipart/form-data">
    @include('sliders._form')
  </form>

@endsection