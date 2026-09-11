@extends('storefront.layouts.app')

@section('title', 'Verify Your Account | ' . config('app.name', 'Restaurant'))

@section('content')
<div class="section section-margin">
  <div class="container">
    <div class="row mb-n10">
      <div class="col-md-3"></div>
      <div class="col-md-6 m-auto m-lg-0 pb-10">
        <div class="login-wrapper" style="margin-bottom: 20%">
          <div class="section-content text-center mb-5">
            <h2 class="title mb-2" style="color: #b22b40;">Verify Your Account</h2>
            <p class="desc-content">We've sent a verification link to <strong>{{ $customer->email }}</strong>. Please check your inbox.</p>
          </div>

          <div id="msg1" class="alert alert-success" style="display:none;text-align:center;"></div>
          <div id="msg2" class="alert alert-danger" style="display:none;text-align:center;"></div>
          <p id="ptime" style="text-align: center; display:none;">You need to wait <span id="time">15</span>s before you can resend</p>

          <div class="text-center">
            <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="resend">Resend Email</button>
            <button type="button" class="btn btn-dark btn-hover-primary rounded-0" id="button12" style="display:none;">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...
            </button>
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
    const email = "{{ $customer->email }}";

    $('#resend').on('click', function () {
        $.ajax({
            url: '{{ route("storefront.verification.resend") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { email: email },
            dataType: 'json',
            beforeSend: function () {
                $('#resend').hide();
                $('#button12').show();
            },
            success: function (response) {
                const $msg = response.code === 200 ? $('#msg1') : $('#msg2');
                $msg.html(response.message).show('slow').delay(5000).hide('slow');

                $('#ptime').show();
                let i = 14;
                const $time = $('#time');
                const timer = setInterval(function () {
                    $time.html(i);
                    if (i === 0) {
                        $('#ptime').hide();
                        $('#resend').show();
                        clearInterval(timer);
                    }
                    i--;
                }, 1000);
            },
            complete: function () {
                $('#button12').hide();
            }
        });
    });
</script>
@endpush