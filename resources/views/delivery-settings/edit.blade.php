{{-- resources/views/delivery-settings/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery Area')

@section('content')
<div class="container-fluid py-4">

    <h1 class="h4 mb-4">Delivery Area</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('delivery-settings.update') }}" method="POST" id="delivery-settings-form">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- Map — search for the restaurant address, drag the marker
                 to fine-tune, drag the radius handle or type a number to
                 resize the circle. All three stay in sync with each other
                 and with the hidden form fields below. --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-0">
                        <input
                            id="delivery-address-search"
                            type="text"
                            class="form-control form-control-lg rounded-0 border-0 border-bottom"
                            placeholder="Search for your restaurant's address…"
                            value="{{ old('address', $setting->address) }}"
                        >
                        <div id="delivery-map" style="height: 480px;"></div>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">Delivery Radius</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    step="0.1"
                                    min="0.5"
                                    max="100"
                                    class="form-control"
                                    id="radius-input"
                                    name="radius_km"
                                    value="{{ old('radius_km', $setting->radius_km) }}"
                                >
                                <span class="input-group-text">km</span>
                            </div>
                            <input type="range" class="form-range mt-2" id="radius-slider"
                                   min="0.5" max="30" step="0.5"
                                   value="{{ old('radius_km', $setting->radius_km) }}">
                            <div class="form-text">Orders outside this radius won't be offered delivery.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Center Point</label>
                            <div class="small text-muted">
                                Lat: <span id="lat-display">{{ $setting->latitude }}</span>,
                                Lng: <span id="lng-display">{{ $setting->longitude }}</span>
                            </div>
                            <div class="small text-muted">
                                Postcode: <span id="postcode-display">{{ $setting->postcode ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is-active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $setting->is_active) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="is-active">Delivery enabled</label>
                        </div>

                        {{-- Hidden fields the map JS keeps in sync --}}
                        <input type="hidden" name="address" id="address-input" value="{{ old('address', $setting->address) }}">
                        <input type="hidden" name="postcode" id="postcode-input" value="{{ old('postcode', $setting->postcode) }}">
                        <input type="hidden" name="latitude" id="latitude-input" value="{{ old('latitude', $setting->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude-input" value="{{ old('longitude', $setting->longitude) }}">

                        <button type="submit" class="btn btn-primary w-100">Save Delivery Area</button>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Seed values for the map — either the saved setting, or a fallback
    // (London) so the map has somewhere sane to center on first setup.
    const deliverySetting = {
        lat: {{ (float) old('latitude', $setting->latitude ?: 51.5072) }},
        lng: {{ (float) old('longitude', $setting->longitude ?: -0.1276) }},
        radiusKm: {{ (float) old('radius_km', $setting->radius_km) }},
    };

    let map, marker, circle, autocomplete;

    function kmToMeters(km) {
        return km * 1000;
    }

    function syncHiddenFields(position) {
        document.getElementById('latitude-input').value = position.lat();
        document.getElementById('longitude-input').value = position.lng();
        document.getElementById('lat-display').textContent = position.lat().toFixed(6);
        document.getElementById('lng-display').textContent = position.lng().toFixed(6);
    }

    function initDeliveryMap() {
        const center = { lat: deliverySetting.lat, lng: deliverySetting.lng };

        map = new google.maps.Map(document.getElementById('delivery-map'), {
            center,
            zoom: 12,
            streetViewControl: false,
            mapTypeControl: false,
        });

        marker = new google.maps.Marker({
            position: center,
            map,
            draggable: true,
            title: 'Drag to set your restaurant location',
        });

        circle = new google.maps.Circle({
            map,
            center,
            radius: kmToMeters(deliverySetting.radiusKm),
            fillColor: '#D12026',
            fillOpacity: 0.15,
            strokeColor: '#D12026',
            strokeWeight: 2,
            editable: true, // gives the user a draggable handle on the circle's edge
        });

        // Marker drag -> move circle center + update hidden fields
        marker.addListener('dragend', function () {
            const pos = marker.getPosition();
            circle.setCenter(pos);
            syncHiddenFields(pos);
        });

        // Dragging the circle's own edge handle -> update the km input
        circle.addListener('radius_changed', function () {
            const km = circle.getRadius() / 1000;
            document.getElementById('radius-input').value = km.toFixed(2);
            document.getElementById('radius-slider').value = Math.min(km, 30);
        });

        // Typing a radius -> resize the circle
        document.getElementById('radius-input').addEventListener('input', function (e) {
            const km = parseFloat(e.target.value);
            if (!isNaN(km) && km > 0) {
                circle.setRadius(kmToMeters(km));
                document.getElementById('radius-slider').value = Math.min(km, 30);
            }
        });

        // Dragging the slider -> resize the circle + update the number input
        document.getElementById('radius-slider').addEventListener('input', function (e) {
            const km = parseFloat(e.target.value);
            circle.setRadius(kmToMeters(km));
            document.getElementById('radius-input').value = km;
        });

        // Address search box (Places Autocomplete) -> move marker + circle + map
        //
        // Deliberately NOT passing `types: ['address']` here — that
        // restricts results to precise street addresses only, which
        // silently excludes business names (type 'establishment', e.g.
        // searching your own restaurant's name) and some postcode-only
        // queries. Google's Autocomplete only allows one type filter
        // collection at a time, so rather than pick one narrow bucket,
        // leaving types unset returns addresses, postcodes, and
        // establishments together — country restriction still keeps it UK-only.
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('delivery-address-search'),
            {
                componentRestrictions: { country: 'gb' }, // UK only
                fields: ['geometry', 'formatted_address', 'address_components'],
            }
        );
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            const pos = place.geometry.location;
            map.setCenter(pos);
            map.setZoom(14);
            marker.setPosition(pos);
            circle.setCenter(pos);
            syncHiddenFields(pos);
            document.getElementById('address-input').value = place.formatted_address ?? '';

            // UK postcode comes back as its own address_component with
            // type 'postal_code' — pull it out explicitly rather than
            // relying on the tail end of the formatted address string.
            const postcodeComponent = (place.address_components || [])
                .find(c => c.types.includes('postal_code'));
            const postcode = postcodeComponent ? postcodeComponent.long_name : '';

            document.getElementById('postcode-input').value = postcode;
            document.getElementById('postcode-display').textContent = postcode || '—';
        });

        syncHiddenFields(marker.getPosition());
    }

    window.initDeliveryMap = initDeliveryMap;
</script>

{{-- Replace YOUR_API_KEY with a real key restricted to Maps JavaScript
     API + Places API, ideally via config('services.google_maps.key')
     rather than hardcoded here. --}}
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key', 'YOUR_API_KEY') }}&libraries=places&callback=initDeliveryMap"
    async
    defer
></script>
@endpush