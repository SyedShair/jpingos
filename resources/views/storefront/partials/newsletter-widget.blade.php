{{--
    Footer newsletter column. In your footer partial, replace the old
    "Subscribe (coming soon)" column with:

        @include('storefront.partials.newsletter-widget')
--}}
<div class="col-12 col-sm-6 col-lg-4 col-xl-4 mb-10">
    <div class="single-footer-widget">
        <h2 class="widget-title">Newsletter</h2>
        <div class="widget-body">
            <p class="desc-content mb-0">Get updates on new dishes and special offers.</p>
            <div class="newsletter-form-wrap pt-4">
                <form id="newsletter-form" action="{{ route('storefront.newsletter.subscribe') }}" method="POST" novalidate>
                    @csrf

                    {{-- Honeypot: hidden from people, tempting to bots. --}}
                    <div style="position:absolute; left:-9999px;" aria-hidden="true">
                        <label>Leave this field empty
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </label>
                    </div>

                    <input type="email" name="email" id="newsletter-email"
                        class="form-control email-box mb-4" placeholder="Enter your email.."
                        autocomplete="email" aria-label="Email address" required>

                    <button class="newsletter-btn btn btn-primary btn-hover-dark" type="submit" id="newsletter-btn">
                        Subscribe
                    </button>

                    <p id="newsletter-message" class="mt-3 mb-0" role="status" aria-live="polite" style="display:none;"></p>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('newsletter-form');
    if (! form) return;

    const btn   = document.getElementById('newsletter-btn');
    const msg   = document.getElementById('newsletter-message');
    const label = btn.textContent.trim();

    function show(text, ok) {
        msg.textContent = text;
        msg.style.color = ok ? '#1a9c5c' : '#D12026';
        msg.style.display = 'block';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        msg.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Subscribing…';

        try {
            // FormData carries the _token field from @csrf.
            const res  = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await res.json().catch(function () { return {}; });

            if (res.ok) {
                show(data.message || 'Thanks for subscribing!', true);
                form.reset();
            } else if (res.status === 422) {
                show((data.errors && data.errors.email && data.errors.email[0]) || 'Please enter a valid email address.', false);
            } else if (res.status === 429) {
                show('Too many attempts — please try again in a minute.', false);
            } else if (res.status === 419) {
                show('Your session expired — please refresh the page and try again.', false);
            } else {
                show('Something went wrong. Please try again.', false);
            }
        } catch (err) {
            show('Could not reach the server. Please try again.', false);
        } finally {
            btn.disabled = false;
            btn.textContent = label;
        }
    });
})();
</script>
@endpush