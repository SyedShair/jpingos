{{--
    "Load more" for the dish listing (menu, category and search pages).

    Usage, in place of {{ $dishes->links() }}:
        @include('storefront.partials.load-more', ['dishes' => $dishes])

    The dish cards must sit directly inside one container with id="dish-grid"
    (or pass a different selector: ['grid' => '#my-grid']). Each click fetches
    the next page in the background, takes the cards out of it and appends
    them to that container, so no changes to your card markup are needed.
--}}

@if ($dishes->hasMorePages())
    <div class="text-center mt-5" id="load-more-wrap">
        <button
            type="button"
            id="load-more-btn"
            class="btn btn-dark px-5 py-3"
            data-next-url="{{ $dishes->nextPageUrl() }}"
            data-grid="{{ $grid ?? '#dish-grid' }}"
        >
            Load more
        </button>
    </div>

    @push('scripts')
    <script>
    (function () {
        const btn  = document.getElementById('load-more-btn');
        const wrap = document.getElementById('load-more-wrap');
        if (! btn) return;

        const grid = document.querySelector(btn.dataset.grid);

        if (! grid) {
            console.warn('Load more: no element matches "' + btn.dataset.grid + '". Add id="dish-grid" to the container that holds the dish cards.');
            return;
        }

        const label = btn.textContent.trim();

        btn.addEventListener('click', async function () {
            const url = btn.dataset.nextUrl;
            if (! url) return;

            btn.disabled = true;
            btn.textContent = 'Loading…';

            try {
                // X-Requested-With marks this as a background request, so
                // visit tracking (which skips AJAX) doesn't count each
                // "Load more" click as a separate page view.
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    credentials: 'same-origin',
                });

                if (! response.ok) throw new Error('HTTP ' + response.status);

                const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
                const nextGrid = doc.querySelector(btn.dataset.grid);
                const cards = nextGrid ? Array.from(nextGrid.children) : [];

                cards.forEach(function (card) {
                    grid.appendChild(document.importNode(card, true));
                });

                // The fetched page has its own button if yet another batch
                // remains; if not, this was the last one.
                const nextBtn = doc.getElementById('load-more-btn');

                if (nextBtn && nextBtn.dataset.nextUrl) {
                    btn.dataset.nextUrl = nextBtn.dataset.nextUrl;
                    btn.disabled = false;
                    btn.textContent = label;
                } else {
                    wrap.remove();
                }

                // For anything on your side that must initialise new cards
                // (e.g. countdown timers) — listen for this event.
                document.dispatchEvent(new CustomEvent('dishes:loaded', { detail: { count: cards.length } }));
            } catch (error) {
                btn.disabled = false;
                btn.textContent = 'Couldn\u2019t load — try again';
            }
        });
    })();
    </script>
    @endpush
@endif