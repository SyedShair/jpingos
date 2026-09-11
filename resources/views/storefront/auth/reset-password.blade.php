@extends('storefront.layouts.app')

@section('title', 'Set New Password | ' . config('app.name', 'Restaurant'))

@section('content')
<div class="section section-margin">
  <div class="container">
    <div class="row mb-n10">
      <div class="col-md-3"></div>
      <div class="col-md-6 m-auto m-lg-0 pb-10">
        <div class="register-wrapper">
          <div class="section-content text-center mb-5">
            <h2 class="title mb-2">Set New Password</h2>
            <p class="desc-content">Please enter your new password below.</p>
          </div>

          <div class="alert alert-success" id="msg1" style="display:none;"></div>
          <div class="alert alert-danger" id="msg2" style="display:none;"></div>

          <form id="reset-form">
            <div class="single-input-item mb-3">
              <input type="password" placeholder="New Password" id="new_password">
              <div id="new_password_error" style="display:none;color:#b22b40;margin-top:-15px;"></div>
            </div>
            <div class="single-input-item mb-3">
              <input type="password" placeholder="Confirm Password" id="new_password_confirmation">
              <div id="confirm_error" style="display:none;color:#b22b40;margin-top:-15px;"></div>
            </div>
            <div class="single-input-item mb-3">
              <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button1">Save</button>
              <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button2" style="display:none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-3"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    $('#button1').on('click', function () {
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#new_password_confirmation').val();
        let hasError = false;

        $('#new_password_error, #confirm_error').hide();

        if (newPassword === '') {
            $('#new_password_error').html('Please enter a new password').show('slow').delay(6000).hide('slow');
            hasError = true;
        }

        if (newPassword !== confirmPassword) {
            $('#confirm_error').html('Passwords do not match').show('slow').delay(6000).hide('slow');
            hasError = true;
        }

        if (hasError) return;

        $.ajax({
            url: "{{ $signedUrl }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { new_password: newPassword, new_password_confirmation: confirmPassword },
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
                    $('#msg2').html(response.message).show('slow').delay(6000).hide('slow');
                    $('#button1').show();
                }
            },
            complete: function () {
                $('#button2').hide();
            }
        });
    });
</script>
@endpush