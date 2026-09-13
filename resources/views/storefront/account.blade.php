@extends('storefront.layouts.app')

@section('title', 'My Account | ' . config('app.name', 'Restaurant'))

@section('content')

    <!-- <div class="section">
        <div class="breadcrumb-area bg-light">
            <div class="container-fluid">
                <div class="breadcrumb-content text-center">
                    <h1 class="title">My Account</h1>
                    <ul>
                        <li><a href="{{ route('storefront.home') }}">Home</a></li>
                        <li class="active">My Account</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <div class="section section-margin">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">

                    <div class="myaccount-page-wrapper">
                        <div class="row">

                            {{-- =================================================
                                 TAB MENU
                            ================================================== --}}

                            <div class="col-lg-3 col-md-4">
                                <div class="myaccount-tab-menu nav" role="tablist">
                                    <a href="#dashboard" class="active" data-bs-toggle="tab">
                                        <i class="fa fa-dashboard"></i> Dashboard
                                    </a>
                                    <a href="#orders" data-bs-toggle="tab">
                                        <i class="fa fa-cart-arrow-down"></i> Orders
                                    </a>
                                    <a href="{{ route('storefront.track-order') }}">
                                        <i class="fa fa-search"></i> Track Order
                                    </a>
                                    <a href="#address-edit" data-bs-toggle="tab">
                                        <i class="fa fa-map-marker"></i> Shipping Address
                                    </a>
                                    <a href="#account-info" data-bs-toggle="tab">
                                        <i class="fa fa-user"></i> Account Details
                                    </a>
                                    <a href="{{ route('storefront.logout') }}">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>
                                </div>
                            </div>

                            {{-- =================================================
                                 TAB CONTENT
                            ================================================== --}}

                            <div class="col-lg-9 col-md-8">
                                <div class="tab-content" id="myaccountContent">

                                    {{-- DASHBOARD --}}
                                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                                        <div class="myaccount-content">
                                            <h3 class="title">Dashboard</h3>
                                            <div class="welcome">
                                                <p>
                                                    Hello, <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                                                    (not you? <a href="{{ route('storefront.logout') }}">Logout</a>)
                                                </p>
                                            </div>
                                            <p class="mb-0">
                                                From your account dashboard you can view your past orders and
                                                update your shipping address and account details.
                                            </p>
                                        </div>
                                    </div>

                                    {{-- ORDERS --}}
                                    <div class="tab-pane fade" id="orders" role="tabpanel">
                                        <div class="myaccount-content">
                                            <h3 class="title">Orders</h3>

                                            @if ($orders->isEmpty())

                                                <p class="mb-0">You haven't placed any orders yet.</p>

                                            @else

                                                <div class="myaccount-table table-responsive text-center">
                                                    <table class="table">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Order</th>
                                                                <th>Date</th>
                                                                <th>Status</th>
                                                                <th>Total</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($orders as $order)
                                                                <tr>
                                                                    <td>{{ $order->order_number }}</td>
                                                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                                                    <td class="text-capitalize">{{ $order->status }}</td>
                                                                    <td>£{{ number_format($order->total, 2) }}</td>
                                                                    <td>
                                                                        <a href="{{ route('storefront.order.confirmation', $order->order_number) }}" class="btn btn-dark btn-hover-primary btn-sm rounded-0">
                                                                            View
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            @endif

                                        </div>
                                    </div>

                                    {{-- SHIPPING ADDRESS --}}
                                    <div class="tab-pane fade" id="address-edit" role="tabpanel">
                                        <div class="myaccount-content">

                                            <div class="alert alert-success text-center" id="address-success" style="display:none;"></div>
                                            <div class="alert alert-danger text-center" id="address-error" style="display:none;"></div>

                                            <h3 class="title">Shipping Address</h3>
                                            <address>
                                                <p><strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong></p>
                                                <p>
                                                    {{ $customer->address ?: 'No address on file yet.' }}
                                                    @if ($customer->apartment), {{ $customer->apartment }}@endif
                                                    <br>
                                                    @if ($customer->city){{ $customer->city }}@endif
                                                    @if ($customer->postcode) {{ $customer->postcode }}@endif
                                                </p>
                                                <p>{{ $customer->phone }}</p>
                                            </address>

                                            <a href="javascript:void(0)" class="btn btn-dark btn-hover-primary rounded-0" data-bs-toggle="modal" data-bs-target="#address-modal">
                                                <i class="fa fa-edit me-2"></i>Edit Address
                                            </a>

                                        </div>
                                    </div>

                                    {{-- ACCOUNT DETAILS --}}
                                    <div class="tab-pane fade" id="account-info" role="tabpanel">
                                        <div class="myaccount-content">
                                            <h3 class="title">Account Details</h3>

                                            <div class="account-details-form">

                                                <form id="update-details-form">

                                                    <div class="alert alert-success text-center" id="details-success" style="display:none;"></div>
                                                    <div class="alert alert-danger text-center" id="details-error" style="display:none;"></div>

                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="single-input-item mb-3">
                                                                <label class="mb-1">First Name</label>
                                                                <input type="text" id="details-first-name" class="form-control" value="{{ $customer->first_name }}">
                                                                <div class="field-error" id="details-first-name-error" style="display:none;color:#b22b40;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="single-input-item mb-3">
                                                                <label class="mb-1">Last Name</label>
                                                                <input type="text" id="details-last-name" class="form-control" value="{{ $customer->last_name }}">
                                                                <div class="field-error" id="details-last-name-error" style="display:none;color:#b22b40;"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="single-input-item mb-3">
                                                        <label class="mb-1">Email Address</label>
                                                        <input type="email" id="details-email" class="form-control" value="{{ $customer->email }}">
                                                        <div class="field-error" id="details-email-error" style="display:none;color:#b22b40;"></div>
                                                    </div>

                                                    <div class="single-input-item mb-3">
                                                        <label class="mb-1">Mobile Number</label>
                                                        <input type="tel" id="details-phone" class="form-control" value="{{ $customer->phone }}">
                                                        <div class="field-error" id="details-phone-error" style="display:none;color:#b22b40;"></div>
                                                    </div>

                                                    <div class="single-input-item">
                                                        <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="save-details-btn">
                                                            Save Changes
                                                        </button>
                                                    </div>

                                                </form>

                                                <form id="update-password-form" class="mt-6">
                                                    <fieldset>
                                                        <legend>Password Change</legend>

                                                        <div class="alert alert-success text-center" id="password-success" style="display:none;"></div>
                                                        <div class="alert alert-danger text-center" id="password-error" style="display:none;"></div>

                                                        <div class="single-input-item mb-3">
                                                            <label class="mb-1">Current Password</label>
                                                            <input type="password" id="password-current" class="form-control">
                                                            <div class="field-error" id="password-current-error" style="display:none;color:#b22b40;"></div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="single-input-item mb-3">
                                                                    <label class="mb-1">New Password</label>
                                                                    <input type="password" id="password-new" class="form-control">
                                                                    <div class="field-error" id="password-new-error" style="display:none;color:#b22b40;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="single-input-item mb-3">
                                                                    <label class="mb-1">Confirm Password</label>
                                                                    <input type="password" id="password-confirm" class="form-control">
                                                                    <div class="field-error" id="password-confirm-error" style="display:none;color:#b22b40;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>

                                                    <div class="single-input-item">
                                                        <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="save-password-btn">
                                                            Save Changes
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- EDIT ADDRESS MODAL --}}
    <div class="modal fade" id="address-modal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 bg-dark text-white">
                    <h5 class="modal-title" id="addressModalLabel" style="color:#fff;">Edit Shipping Address</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control" id="modal-address" value="{{ $customer->address }}">
                        <div class="field-error" id="modal-address-error" style="display:none;color:#b22b40;"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Apartment, suite, unit etc. (optional)</label>
                        <input type="text" class="form-control" id="modal-apartment" value="{{ $customer->apartment }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>City</label>
                                <input type="text" class="form-control" id="modal-city" value="{{ $customer->city }}">
                                <div class="field-error" id="modal-city-error" style="display:none;color:#b22b40;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Postcode</label>
                                <input type="text" class="form-control" id="modal-postcode" value="{{ $customer->postcode }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" id="modal-phone" value="{{ $customer->phone }}">
                        <div class="field-error" id="modal-phone-error" style="display:none;color:#b22b40;"></div>
                    </div>

                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-start">
                    <button type="button" id="save-address-btn" class="btn btn-dark">Update</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";

    function csrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function showAlert($el, message) {
        $el.text(message).show();
        setTimeout(function () { $el.fadeOut(); }, 6000);
    }

    function clearFieldErrors(prefix) {
        $('[id^="' + prefix + '"][id$="-error"]').hide().text('');
    }

    function showFieldError(id, message) {
        $('#' + id + '-error').text(message).show();
    }

    /* =========================================================
       ACCOUNT DETAILS
    ========================================================== */

    $('#save-details-btn').on('click', function () {

        const $btn = $(this);
        clearFieldErrors('details-');

        const firstName = $('#details-first-name').val().trim();
        const lastName = $('#details-last-name').val().trim();
        const email = $('#details-email').val().trim();
        const phone = $('#details-phone').val().trim();

        let hasError = false;

        if (firstName === '') { showFieldError('details-first-name', 'Please enter your first name.'); hasError = true; }
        if (lastName === '') { showFieldError('details-last-name', 'Please enter your last name.'); hasError = true; }
        if (email === '') { showFieldError('details-email', 'Please enter your email.'); hasError = true; }
        if (phone === '') { showFieldError('details-phone', 'Please enter your mobile number.'); hasError = true; }

        if (hasError) return;

        $btn.prop('disabled', true).text('Saving...');

        fetch('{{ route('storefront.account.update-details') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ first_name: firstName, last_name: lastName, email: email, phone: phone })
        })
        .then(res => res.json())
        .then(data => {
            if (data.code === 200) {
                showAlert($('#details-success'), data.message);
            } else {
                showAlert($('#details-error'), data.message);
            }
        })
        .catch(() => showAlert($('#details-error'), 'Something went wrong. Please try again.'))
        .finally(() => $btn.prop('disabled', false).text('Save Changes'));

    });

    /* =========================================================
       PASSWORD CHANGE
    ========================================================== */

    $('#save-password-btn').on('click', function () {

        const $btn = $(this);
        clearFieldErrors('password-');

        const current = $('#password-current').val();
        const newPwd = $('#password-new').val();
        const confirm = $('#password-confirm').val();

        let hasError = false;

        if (current === '') { showFieldError('password-current', 'Please enter your current password.'); hasError = true; }
        if (newPwd === '') { showFieldError('password-new', 'Please enter a new password.'); hasError = true; }
        if (confirm === '') { showFieldError('password-confirm', 'Please confirm your new password.'); hasError = true; }
        if (newPwd && confirm && newPwd !== confirm) { showFieldError('password-confirm', 'Passwords do not match.'); hasError = true; }

        if (hasError) return;

        $btn.prop('disabled', true).text('Saving...');

        fetch('{{ route('storefront.account.update-password') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ current: current, new: newPwd, confirm: confirm })
        })
        .then(res => res.json())
        .then(data => {
            if (data.code === 200) {
                showAlert($('#password-success'), data.message);
                $('#update-password-form')[0].reset();
            } else {
                showAlert($('#password-error'), data.message);
            }
        })
        .catch(() => showAlert($('#password-error'), 'Something went wrong. Please try again.'))
        .finally(() => $btn.prop('disabled', false).text('Save Changes'));

    });

    /* =========================================================
       ADDRESS MODAL
    ========================================================== */

    $('#save-address-btn').on('click', function () {

        const $btn = $(this);
        clearFieldErrors('modal-');

        const address = $('#modal-address').val().trim();
        const apartment = $('#modal-apartment').val().trim();
        const city = $('#modal-city').val().trim();
        const postcode = $('#modal-postcode').val().trim();
        const phone = $('#modal-phone').val().trim();

        let hasError = false;

        if (address === '') { showFieldError('modal-address', 'Please enter your address.'); hasError = true; }
        if (city === '') { showFieldError('modal-city', 'Please enter your city.'); hasError = true; }
        if (phone === '') { showFieldError('modal-phone', 'Please enter your mobile number.'); hasError = true; }

        if (hasError) return;

        $btn.prop('disabled', true).text('Updating...');

        fetch('{{ route('storefront.account.update-address') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ address: address, apartment: apartment || null, city: city, postcode: postcode || null, phone: phone })
        })
        .then(res => res.json())
        .then(data => {
            if (data.code === 200) {
                // Reload so the address tab's display and the dashboard
                // greeting reflect the saved values immediately.
                window.location.reload();
            } else {
                showAlert($('#address-error'), data.message);
            }
        })
        .catch(() => showAlert($('#address-error'), 'Something went wrong. Please try again.'))
        .finally(() => $btn.prop('disabled', false).text('Update'));

    });

})(jQuery);
</script>
@endpush