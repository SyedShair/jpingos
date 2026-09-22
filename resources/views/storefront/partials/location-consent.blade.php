{{-- Optional precise-location prompt. Include once in your storefront layout, just before </body>:
        @include('storefront.partials.location-consent')
     The layout <head> must contain:  <meta name="csrf-token" content="{{ csrf_token() }}">
     Nothing is requested until the visitor clicks "Allow". --}}
<div id="loc-consent" style="display:none;position:fixed;left:16px;right:16px;bottom:16px;max-width:420px;z-index:9999;background:#fff;color:#111827;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:14px 16px;font:14px/1.4 system-ui,sans-serif">
    <p style="margin:0 0 10px">Share your location so we can check delivery to your area faster? We only use it to improve your experience.</p>
    <div style="display:flex;gap:8px">
        <button id="loc-allow" type="button" style="padding:8px 14px;border:0;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer">Allow</button>
        <button id="loc-deny" type="button" style="padding:8px 14px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#111827;cursor:pointer">No thanks</button>
    </div>
</div>

<script>
(function () {
    var KEY = 'loc_consent_v1';
    var box = document.getElementById('loc-consent');
    if (!box || !('geolocation' in navigator)) return;

    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) {}
    if (saved) return;                                   // already answered

    setTimeout(function () { box.style.display = 'block'; }, 4000);

    function remember(v) {
        try { localStorage.setItem(KEY, v); } catch (e) {}
        box.style.display = 'none';
    }

    document.getElementById('loc-deny').onclick = function () { remember('denied'); };

    document.getElementById('loc-allow').onclick = function () {
        navigator.geolocation.getCurrentPosition(function (p) {
            remember('granted');
            var token = document.querySelector('meta[name="csrf-token"]');
            fetch(@json(route('storefront.visitor.location')), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token ? token.content : ''
                },
                body: JSON.stringify({ lat: p.coords.latitude, lng: p.coords.longitude })
            });
        }, function () { remember('denied'); }, { timeout: 10000, maximumAge: 600000 });
    };
})();
</script>
