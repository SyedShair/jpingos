@extends('storefront.layouts.app')

@section('title', 'Create Account | ' . config('app.name', 'Restaurant'))

@section('content')

    <div class="section section-margin">
        <div class="container">

            <div class="row mb-n10">
                <div class="col-md-3"></div>

                <div class="col-md-6 m-auto m-lg-0 pb-10">
                    <div id="register_content">
                        <div class="register-wrapper">

                            <div class="section-content text-center mb-5">
                                <h2 class="title mb-2">Create Account</h2>
                                <p class="desc-content">Please register using your account details below.</p>
                            </div>

                            <form id="lform">

                                <div class="col-md-12">
                                    <div class="alert alert-success" id="msg1"></div>
                                    <div class="alert alert-danger" id="msg2"></div>
                                </div>

                                <div class="single-input-item mb-3" id="n-class">
                                    <input type="text" placeholder="First Name" name="first_name" id="fname">
                                    <div id="fname_error" style="display: none;margin-top: -15px;color: #b22b40;"></div>
                                </div>

                                <div class="single-input-item mb-3" id="l-class">
                                    <input type="text" placeholder="Last Name" name="last_name" id="lname">
                                    <div id="lname_error" style="display: none;margin-top: -15px;color: #b22b40;"></div>
                                </div>

                                <div class="single-input-item mb-3" id="e-class">
                                    <input type="email" placeholder="Email Address" name="email" id="email">
                                    <div id="email_error" style="display: none;margin-top: -15px;color: #b22b40;"></div>
                                </div>

                                <div class="single-input-item mb-3" id="ph-class">
                                    <input type="tel" placeholder="Phone Number (optional)" name="phone" id="phone">
                                </div>

                                <div class="single-input-item mb-3" id="p-class">
                                    <input type="password" placeholder="Password" name="password" id="password">
                                    <div id="password_error" style="display: none;margin-top: -15px;color: #b22b40;"></div>
                                </div>

                                <div class="single-input-item mb-3" id="c-class">
                                    <input type="password" placeholder="Confirm Password" name="password_confirmation" id="password_confirmation">
                                    <div id="password_confirmation_error" style="display: none;margin-top: -15px;color: #b22b40;"></div>
                                </div>

                                <div class="single-input-item mb-3">
                                    <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button1" onclick="save()">Register</button>
                                    <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button2" style="display:none;">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button>
                                </div>

                                <div class="lost-password mt-4" style="text-align: center">
                                    <a href="{{ route('storefront.login') }}">Already have an account? Log in</a>
                                </div>

                            </form>

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

    function save() {
        const fname = $('#fname').val();
        const lname = $('#lname').val();
        const email = $('#email').val();
        const phone = $('#phone').val();
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        let isError = '';

        $('#n-class, #l-class, #e-class, #p-class, #c-class').removeClass('single-input-item-error').addClass('single-input-item');
        $('#fname_error, #lname_error, #email_error, #password_error, #password_confirmation_error').hide();

        if (fname === '') {
            $('#n-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#fname_error').html('Please Enter First Name').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (lname === '') {
            $('#l-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#lname_error').html('Please Enter Last Name').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (email === '') {
            $('#e-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#email_error').html('Please Enter Email Address').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (password === '') {
            $('#p-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#password_error').html('Please Enter Password').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (confirmPassword === '') {
            $('#c-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#password_confirmation_error').html('Please Confirm Your Password').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (password !== confirmPassword && confirmPassword !== '') {
            $('#password_error').html('Passwords do not match.').show('slow').delay(8000).hide('slow');
            $('#password_confirmation_error').html('Passwords do not match.').show('slow').delay(8000).hide('slow');
            isError = 'yes';
        }

        if (isError === '') {
            $.ajax({
                url: '{{ route("storefront.register") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    first_name: fname,
                    last_name: lname,
                    email: email,
                    phone: phone,
                    password: password,
                    password_confirmation: confirmPassword
                },
                cache: false,
                dataType: 'json',
                beforeSend: function () {
                    $('#button2').show();
                    $('#button1').hide();
                },
                success: function (response) {
                    if (response.code === 202) {
                        $('#e-class').removeClass('single-input-item').addClass('single-input-item-error');
                        $('#email_error').html(response.message).show('slow').delay(8000).hide('slow');
                    } else if (response.code === 200) {
                        $('#msg1').html(response.message).show('slow');
                        window.location.href = response.redirect;
                    } else {
                        $('#msg2').html(response.message || 'Something went wrong.').show('slow').delay(5000).hide('slow');
                    }
                },
                complete: function () {
                    $('#button2').hide();
                    $('#button1').show();
                }
            });
        }
    }
</script>
@endpush