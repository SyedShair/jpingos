@extends('storefront.layouts.app')

@section('title', 'Reset Password | ' . config('app.name', 'Restaurant'))

@section('content')
<div class="section section-margin">
  <div class="container">
    <div class="row mb-n10">
      <div class="col-md-3"></div>
      <div class="col-md-6 m-auto m-lg-0 pb-10">
        <div class="login-wrapper">
          <div class="section-content text-center mb-5">
            <h2 class="title mb-2">Reset Password</h2>
            <p class="desc-content">Please enter your email below.</p>
          </div>

          <div class="alert alert-success text-center" id="msg1" style="display:none;"></div>
          <div class="alert alert-danger text-center" id="msg2" style="display:none;"></div>
          <p id="ptime" style="text-align: center; display:none;">You need to wait <span id="time">15</span>s before you can resend</p>

          <form>
            <div id="submit_email">
              <div class="single-input-item mb-3" id="e-class">
                <input type="email" placeholder="Email" id="email">
                <div id="email_error" style="display: none;color: #b22b40;margin-top:-15px;"></div>
              </div>
              <div class="single-input-item mb-3">
                <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button">Send Email</button>
                <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button3" style="display:none;">Resend Email</button>
                <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button2" style="display:none;">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                </button>
              </div>
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
    $('.btn').filter('#button, #button3').on('click', function () {
        const email = $('#email').val();

        if (email === '') {
            $('#e-class').removeClass('single-input-item').addClass('single-input-item-error');
            $('#email_error').html('Please Enter Email Address').show('slow').delay(8000).hide('slow');
            return;
        }

        $.ajax({
            url: '{{ route("storefront.password.send") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { email: email },
            dataType: 'json',
            beforeSend: function () {
                $('#button, #button3').hide();
                $('#button2').show();
            },
            success: function (response) {
                $('#submit_email').hide();
                $('#msg1').html(response.message).show('slow').delay(8000).hide('slow');

                $('#ptime').show();
                let i = 14;
                const $time = $('#time');
                const timer = setInterval(function () {
                    $time.html(i);
                    if (i === 0) {
                        $('#ptime').hide();
                        $('#submit_email').show();
                        $('#button').hide();
                        $('#button3').show();
                        clearInterval(timer);
                    }
                    i--;
                }, 1000);
            },
            complete: function () {
                $('#button2').hide();
            }
        });
    });
</script>
@endpush