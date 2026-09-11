@extends('storefront.layouts.app')

@section('title', 'Log In | ' . config('app.name', 'Restaurant'))

@section('content')

    <div class="section section-margin">
        <div class="container">

            <div class="row mb-n10">
                <div class="col-md-3"></div>
                <div class="col-md-6 m-auto m-lg-0 pb-10">
                    <div id="login_verify">
                        <div class="login-wrapper">

                            <div class="section-content text-center mb-5">
                                <h2 class="title mb-2">Login</h2>
                                <p class="desc-content">Please login using your account details below.</p>
                            </div>

                            <div id="load">
                                <form id="lform">
                                    @csrf

                                    <div class="single-input-item mb-3">
                                        @if (session('status'))
                                            <div class="alert alert-success" style="text-align: center">{{ session('status') }}</div>
                                        @endif

                                        <div class="alert alert-success" id="msg1" style="text-align: center"></div>
                                        <div class="alert alert-danger" id="msg2" style="text-align: center"></div>
                                    </div>

                                    <div class="single-input-item mb-3" id="class1">
                                        <input type="email" placeholder="Email" name="email" id="email">
                                        <div id="email_error" style="display: none;color: #b22b40;margin-top: -15px;"></div>
                                    </div>

                                    <div class="single-input-item mb-3" id="class2">
                                        <input type="password" placeholder="Password" name="password" id="password">
                                        <div id="password_error" style="display: none;color: #b22b40;margin-top: -15px;"></div>
                                    </div>

                                    <div class="single-input-item mb-3">
                                        <div class="login-reg-form-meta d-flex align-items-center justify-content-between">
                                            <div class="remember-meta mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="rememberMe">
                                                    <label class="custom-control-label" for="rememberMe">Remember Me</label>
                                                </div>
                                                 <a href="{{route('storefront.password.request')}}" class="forget-pwd mb-3">Reset Password?</a>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-lg btn-dark btn-hover-primary rounded-0" id="button1" onclick="check()">Login</button>
                                            <button type="button" class="btn btn-lg btn-dark btn-hover-primary rounded-0" id="button2" disabled style="display:none;">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                            </button>
                                        </div>
                                    </div>

                                    <div class="lost-password mt-4" style="text-align: center">
                                        <a href="{{ route('storefront.register') }}">Create Account</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    $('#msg1').hide();
    $('#msg2').hide();

    $('#lform').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            check();
        }
    });

    function check() {
        const email = $('#email').val();
        const password = $('#password').val();
        let isError = '';

        $('#class1, #class2').removeClass('single-input-item-error').addClass('single-input-item');
        $('#email_error, #password_error').hide();

        if (email === '') {
            $('#class1').removeClass('single-input-item').addClass('single-input-item-error');
            $('#email_error').html('Please Enter Email Address');
            $('#email_error').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (password === '') {
            $('#class2').removeClass('single-input-item').addClass('single-input-item-error');
            $('#password_error').html('Please Enter Password');
            $('#password_error').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (isError === '') {
            $.ajax({
                url: '{{ route("storefront.login") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { email: email, password: password },
                cache: false,
                dataType: 'json',
                beforeSend: function () {
                    $('#button1').hide();
                    $('#button2').show();
                },
                success: function (response) {
                    if (response.code === 200) {
                        $('#msg1').html(response.message).show('slow');
                        window.location.href = response.redirect;
                    } else {
                        $('#msg2').html(response.message).show('slow').delay(5000).hide('slow');
                    }
                },
                complete: function () {
                    $('#button1').show();
                    $('#button2').hide();
                }
            });
        }
    }
</script>
@endpush