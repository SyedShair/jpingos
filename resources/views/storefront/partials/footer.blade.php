<footer class="section footer-section">
    <div class="footer-top section-padding">
        <div class="container">
            <div class="row mb-n10">
                <div class="col-12 col-sm-6 col-lg-4 col-xl-4 mb-10">
                    <div class="single-footer-widget">
                        <h2 class="widget-title">Contact Us</h2>
                        <p class="desc-content">Come in, call ahead, or order online for pickup and delivery.</p>
                        <ul class="widget-address">
                            <li><span>Address: </span> 123 Main Street, Anytown, ST 12345</li>
                            <li><span>Call to: </span> <a href="tel:+10123456789">+1 (012) 345-6789</a></li>
                            <li><span>Mail to: </span> <a href="mailto:hello@example.com">hello@example.com</a></li>
                        </ul>
                        <div class="widget-social justify-content-start mt-4">
                            <a title="Facebook" href="#"><i class="fa fa-facebook-f"></i></a>
                            <a title="Instagram" href="#"><i class="fa fa-instagram"></i></a>
                            <a title="Twitter" href="#"><i class="fa fa-twitter"></i></a>
                        </div>
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
                            <li>Mon – Fri: 9:00 – 22:00</li>
                            <li>Sat – Sun: 10:00 – 23:00</li>
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
                        <p class="mb-0">Copyright &copy; {{ date('Y') }} {{ config('app.name', 'Restaurant') }}. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
