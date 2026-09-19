@php
    $footerSettings = \App\Models\Setting::current();
@endphp

<footer class="section footer-section">
    <div class="footer-top section-padding">
        <div class="container">
            <div class="row mb-n10">
                <div class="col-12 col-sm-6 col-lg-4 col-xl-4 mb-10">
                    <div class="single-footer-widget">
                        <h2 class="widget-title">Contact Us</h2>
                        <p class="desc-content">Come in, call ahead, or order online for pickup and delivery.</p>
                        <ul class="widget-address">
                            @if ($footerSettings->phone)
                                <li><span>Call to: </span> <a href="tel:{{ $footerSettings->phone }}">{{ $footerSettings->phone }}</a></li>
                            @endif
                            @if ($footerSettings->whatsapp)
                                <li><span>WhatsApp: </span> <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerSettings->whatsapp) }}" target="_blank" rel="noopener">{{ $footerSettings->whatsapp }}</a></li>
                            @endif
                            @if ($footerSettings->email)
                                <li><span>Mail to: </span> <a href="mailto:{{ $footerSettings->email }}">{{ $footerSettings->email }}</a></li>
                            @endif
                            
                        </ul>

                        @if ($footerSettings->facebook || $footerSettings->instagram)
                            <div class="widget-social justify-content-start mt-4">
                                @if ($footerSettings->facebook)
                                    <a title="Facebook" href="{{ $footerSettings->facebook }}" target="_blank" rel="noopener"><i class="fa fa-facebook-f"></i></a>
                                @endif
                                @if ($footerSettings->instagram)
                                    <a title="Instagram" href="{{ $footerSettings->instagram }}" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-2 col-xl-2 mb-10">
                    <div class="single-footer-widget">
                        <h2 class="widget-title">Menu</h2>
                        <ul class="widget-list">
                            @foreach ($categories ?? [] as $main)
                                <li><a href="#">{{ $main->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-2 col-xl-2 mb-10">
                    <div class="single-footer-widget">
                        <h2 class="widget-title">Hours</h2>
                        <ul class="widget-list">
                            @forelse ($footerSettings->groupedOpeningHours() as $group)
                                <li>{{ $group['label'] }}: {{ $group['hours'] }}</li>
                            @empty
                                <li>Hours not set yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4 col-xl-4 mb-10">
                    <div class="single-footer-widget">
                        <h2 class="widget-title">Newsletter</h2>
                        <div class="widget-body">
                            <p class="desc-content mb-0">Get updates on new dishes and special offers.</p>
                            <div class="newsletter-form-wrap pt-4">
                                <form>
                                    <input type="email" class="form-control email-box mb-4" placeholder="Enter your email..">
                                    <button class="newsletter-btn btn btn-primary btn-hover-dark" type="button" disabled>
                                        Subscribe (coming soon)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <div class="copyright-content">
                        <p class="mb-0">
                            Copyright &copy; {{ date('Y') }}
                            {{ $footerSettings->site_name ?: config('app.name', 'Restaurant') }}. All Rights Reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>