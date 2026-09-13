@extends('storefront.layouts.app')

@section('title', 'Track Your Order | ' . config('app.name', 'Restaurant'))

@push('styles')
<style>
    .track-order-page {
        --tp-green: #1a9c5c;
        --tp-red: #b22b40;
        --tp-charcoal: #1a1a1a;
        --tp-muted: #6b7280;
        --tp-line: #e5e5e5;
    }

    .track-order-card {
        background: #fff;
        border: 1px solid var(--tp-line);
        border-radius: 14px;
        padding: 40px 36px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
    }

    .track-order-card .track-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 16px;
        border-radius: 50%;
        background: rgba(26, 156, 92, .1);
        color: var(--tp-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .track-order-card .single-input-item input {
        border: 1px solid var(--tp-line);
        border-radius: 8px;
        padding: 12px 16px;
        width: 100%;
        font-size: 14px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .track-order-card .single-input-item input:focus {
        outline: none;
        border-color: var(--tp-green);
        box-shadow: 0 0 0 3px rgba(26, 156, 92, .12);
    }

    .track-order-card button[type="submit"] {
        border-radius: 8px !important;
        padding: 14px;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
        font-size: 13px;
        transition: background .2s ease, transform .15s ease;
    }

    .track-order-card button[type="submit"]:hover {
        transform: translateY(-1px);
    }

    #track-order-error {
        border-radius: 8px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')

    

    <div class="section section-margin track-order-page">
        <div class="container">

            <div class="row">
                <div class="col-lg-6 mx-auto" id="track-order-form-wrapper">

                    <div class="track-order-card">

                        <div class="text-center mb-4">
                            <div class="track-icon">
                                <i class="fa fa-search"></i>
                            </div>
                            <h4 class="mb-2">Where's my order?</h4>
                            <p class="desc-content text-muted mb-0">
                                Enter your order number and the email you used at checkout to view its status.
                            </p>
                        </div>

                        <div class="alert alert-danger d-none" id="track-order-error"></div>

                        <form method="POST" action="{{ route('storefront.track-order.lookup') }}" id="track-order-form">
                            @csrf

                            <div class="single-input-item mb-3">
                                <input type="text" name="order_number" placeholder="Order Number (e.g. ORD-XXXXXXXX)">
                            </div>

                            <div class="single-input-item mb-3">
                                <input type="email" name="email" placeholder="Email">
                            </div>

                            <div class="single-input-item">
                                <button type="submit" class="btn btn-dark btn-hover-primary w-100" id="track-order-submit">
                                    Track Order
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

                <div class="col-12 d-none" id="track-order-result-wrapper"></div>

            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";

    const $formWrapper = $('#track-order-form-wrapper');
    const $resultWrapper = $('#track-order-result-wrapper');
    const $errorBox = $('#track-order-error');
    const $form = $('#track-order-form');
    const $submitBtn = $('#track-order-submit');

    function showForm() {
        $resultWrapper.addClass('d-none').empty();
        $formWrapper.removeClass('d-none');
    }

    function showResult(html) {
        $formWrapper.addClass('d-none');
        $resultWrapper.html(html).removeClass('d-none');
    }

    $form.on('submit', function (e) {
        e.preventDefault();

        $errorBox.addClass('d-none').text('');

        const original = $submitBtn.html();
        $submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Looking up...'
        );

        fetch($form.attr('action'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                order_number: $form.find('[name="order_number"]').val(),
                email: $form.find('[name="email"]').val()
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showResult(data.html);
            } else {
                $errorBox.removeClass('d-none').text(data.message || 'We couldn\'t find that order.');
            }
        })
        .catch(() => {
            $errorBox.removeClass('d-none').text('Something went wrong. Please try again.');
        })
        .finally(() => {
            $submitBtn.prop('disabled', false).html(original);
        });
    });

    // The "Track a different order" link lives inside the AJAX-injected
    // result partial, so this has to be a delegated handler rather than
    // a direct one bound at page load.
    $(document).on('click', '#track-another-order', function (e) {
        e.preventDefault();
        $form[0].reset();
        showForm();
    });

})(jQuery);
</script>
@endpush