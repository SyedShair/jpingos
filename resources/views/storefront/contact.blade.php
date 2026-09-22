@extends('storefront.layouts.app')

@section('title', 'Contact Us | ' . config('app.name', 'Restaurant'))

@php
    $contactSettings = \App\Models\Setting::current();
@endphp

@section('content')


<!-- Contact Us Section Start -->
<div class="section section-margin">
    <div class="container">
        <div class="row mb-n10">
            <div class="col-12 col-lg-8 mb-10">
                <!-- Section Title Start -->
                <div class="section-title" data-aos="fade-up" data-aos-delay="300">
                    <h2 class="title pb-3">Get In Touch</h2>
                    <span></span>
                    <div class="title-border-bottom"></div>
                </div>
                <!-- Section Title End -->

                @if (session('status-message'))
                    <div class="alert alert-success">{{ session('status-message') }}</div>
                @endif

                <!-- Contact Form Wrapper Start -->
                <div class="contact-form-wrapper contact-form">
                    <div class="alert alert-success" id="contact-success" style="display:none;"></div>
                    <div class="alert alert-danger" id="contact-error" style="display:none;"></div>

                    <form id="contact-form">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                                        <div class="input-item mb-4">
                                            <input class="input-item" type="text" id="contact-name" placeholder="Your Name *">
                                            <div class="field-error" id="contact-name-error" style="display:none;color:#b22b40;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                        <div class="input-item mb-4">
                                            <input class="input-item" type="email" id="contact-email" placeholder="Email *">
                                            <div class="field-error" id="contact-email-error" style="display:none;color:#b22b40;"></div>
                                        </div>
                                    </div>
                                    <div class="col-12" data-aos="fade-up" data-aos-delay="300">
                                        <div class="input-item mb-4">
                                            <input class="input-item" type="text" id="contact-subject" placeholder="Subject *">
                                            <div class="field-error" id="contact-subject-error" style="display:none;color:#b22b40;"></div>
                                        </div>
                                    </div>
                                    <div class="col-12" data-aos="fade-up" data-aos-delay="400">
                                        <div class="input-item mb-8">
                                            <textarea class="textarea-item" id="contact-message" placeholder="Message"></textarea>
                                            <div class="field-error" id="contact-message-error" style="display:none;color:#b22b40;"></div>
                                        </div>
                                    </div>
                                    <div class="col-12" data-aos="fade-up" data-aos-delay="500">
                                        <button type="button" id="contact-submit" class="btn btn-dark btn-hover-primary rounded-0">
                                            Send A Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Contact Form Wrapper End -->
            </div>

            <div class="col-12 col-lg-4 mb-10">
                <!-- Section Title Start -->
                <div class="section-title" data-aos="fade-up" data-aos-delay="300">
                    <h2 class="title pb-3">Contact Info</h2>
                    <span></span>
                    <div class="title-border-bottom"></div>
                </div>
                <!-- Section Title End -->

                <!-- Contact Information Wrapper Start -->
                <div class="row contact-info-wrapper mb-n6">

                    <div class="col-lg-12 col-md-6 col-sm-12 col-12 single-contact-info mb-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="single-contact-icon">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <div class="single-contact-title-content">
                            <h4 class="title">Postal Address</h4>
                            <p class="desc-content">{{ $contactSettings->address ?: 'Address not set yet.' }}</p>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-6 col-sm-12 col-12 single-contact-info mb-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="single-contact-icon">
                            <i class="fa fa-mobile"></i>
                        </div>
                        <div class="single-contact-title-content">
                            <h4 class="title">Contact Us Anytime</h4>
                            <p class="desc-content">
                                @if ($contactSettings->phone)
                                    Phone: <a href="tel:{{ $contactSettings->phone }}">{{ $contactSettings->phone }}</a>
                                @else
                                    Phone not set yet.
                                @endif
                                @if ($contactSettings->whatsapp)
                                    <br>WhatsApp: <a href="https://wa.me/{{ preg_replace('/\D/', '', $contactSettings->whatsapp) }}" target="_blank" rel="noopener">{{ $contactSettings->whatsapp }}</a>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-6 col-sm-12 col-12 single-contact-info mb-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="single-contact-icon">
                            <i class="fa fa-envelope-o"></i>
                        </div>
                        <div class="single-contact-title-content">
                            <h4 class="title">Support</h4>
                            <p class="desc-content">
                                @if ($contactSettings->email)
                                    <a href="mailto:{{ $contactSettings->email }}">{{ $contactSettings->email }}</a>
                                @else
                                    Email not set yet.
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
                <!-- Contact Information Wrapper End -->
            </div>
        </div>
    </div>
</div>
<!-- Contact us Section End -->

<!-- Contact Map Start -->
<div class="section" data-aos="fade-up" data-aos-delay="300">
    @if ($contactSettings->map_url)
        <div class="google-map-area w-100">
            <iframe class="contact-map" src="{{ $contactSettings->map_url }}"></iframe>
        </div>
    @endif
</div>
<!-- Contact Map End -->

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";

    function showFieldError(id, message) {
        $('#' + id + '-error').text(message).show();
    }

    function clearFieldErrors() {
        $('.field-error').hide().text('');
    }

    $('#contact-submit').on('click', function () {

        const $btn = $(this);
        clearFieldErrors();
        $('#contact-success').hide();
        $('#contact-error').hide();

        const name = $('#contact-name').val().trim();
        const email = $('#contact-email').val().trim();
        const subject = $('#contact-subject').val().trim();
        const message = $('#contact-message').val().trim();

        let hasError = false;

        if (name === '') { showFieldError('contact-name', 'Please enter your name.'); hasError = true; }
        if (email === '') {
            showFieldError('contact-email', 'Please enter your email.'); hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('contact-email', 'Please enter a valid email.'); hasError = true;
        }
        if (subject === '') { showFieldError('contact-subject', 'Please enter a subject.'); hasError = true; }
        if (message === '') { showFieldError('contact-message', 'Please enter a message.'); hasError = true; }

        if (hasError) return;

        $btn.prop('disabled', true).text('Sending...');

        fetch('{{ route('storefront.contact.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ name, email, subject, message })
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (err) {
                    throw new Error(err.message || 'Something went wrong. Please try again.');
                });
            }
            return response.json();
        })
        .then(function (data) {
            $('#contact-success').text(data.message).show();
            $('#contact-form')[0].reset();
        })
        .catch(function (error) {
            $('#contact-error').text(error.message).show();
        })
        .finally(function () {
            $btn.prop('disabled', false).text('Send A Message');
        });

    });

})(jQuery);
</script>
@endpush