{{-- resources/views/delivery-check.blade.php --}}
@extends('layouts.app')

@section('title', 'Do We Deliver To You? — ' . config('app.name'))

@section('content')
<div class="section delivery-check-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">

                <h1 class="mb-2">Do We Deliver To You?</h1>
                <p class="text-muted mb-4">
                    Pop in your postcode and we'll tell you straight away
                    whether you're inside our delivery area.
                </p>

                <div class="d-flex justify-content-center">
                    @include('storefront.partials.delivery-check-widget')
                </div>

                @if (! $setting->is_active)
                    <p class="text-muted small mt-4">
                        Delivery is currently paused — you can still check your
                        area for when we're back online.
                    </p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection