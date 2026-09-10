@extends('layouts.app')

@section('title', 'Delivery Area & Pricing')

@section('content')
<div class="container-fluid py-4">

    <h1 class="h4 mb-4">Delivery Area & Pricing</h1>

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

    <form action="{{ route('admin.delivery-settings.update') }}" method="POST" id="delivery-settings-form">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- Left Column: Map --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <input
                            id="delivery-address-search"
                            type="text"
                            class="form-control form-control-lg rounded-0 border-0 border-bottom"
                            placeholder="Search for your restaurant's address…"
                            value="{{ old('address', $setting->address) }}"
                        >
                        <div id="delivery-map" style="height: 520px;"></div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Controls & Dynamic Pricing --}}
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title h6 mb-3 fw-bold">Delivery Zone Radius</h5>

                        <div class="mb-3">
                            <label class="form-label">Max Delivery Radius</label>
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
                            <div class="form-text">Orders beyond this radius will be rejected at checkout.</div>
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
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title h6 mb-3 fw-bold">Distance Charges (Uber-style)</h5>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Base Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="base_price" value="{{ old('base_price', $setting->base_price) }}">
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Base Distance</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" class="form-control"
                                           name="base_km" value="{{ old('base_km', $setting->base_km) }}">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Extra Cost / km</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="per_km_price" value="{{ old('per_km_price', $setting->per_km_price) }}">
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Max Fee Cap</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0" class="form-control" placeholder="Optional"
                                           name="max_delivery_fee" value="{{ old('max_delivery_fee', $setting->max_delivery_fee) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            Example: £{{ number_format($setting->base_price, 2) }} covers the first {{ $setting->base_km }} km.
                            Every km after that adds £{{ number_format($setting->per_km_price, 2) }}.
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is-active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $setting->is_active) ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-bold" for="is-active">Delivery enabled</label>
                        </div>

                        {{-- Hidden inputs synced by Maps JS --}}
                        <input type="hidden" name="address" id="address-input" value="{{ old('address', $setting->address) }}">
                        <input type="hidden" name="postcode" id="postcode-input" value="{{ old('postcode', $setting->postcode) }}">
                        <input type="hidden" name="latitude" id="latitude-input" value="{{ old('latitude', $setting->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude-input" value="{{ old('longitude', $setting->longitude) }}">

                        <button type="submit" class="btn btn-primary w-100">Save Settings</button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
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
            editable: true,
        });

        marker.addListener('dragend', function () {
            const pos = marker.getPosition();
            circle.setCenter(pos);
            syncHiddenFields(pos);
        });

        circle.addListener('radius_changed', function () {
            const km = circle.getRadius() / 1000;
            document.getElementById('radius-input').value = km.toFixed(2);
            document.getElementById('radius-slider').value = Math.min(km, 30);
        });

        document.getElementById('radius-input').addEventListener('input', function (e) {
            const km = parseFloat(e.target.value);
            if (!isNaN(km) && km > 0) {
                circle.setRadius(kmToMeters(km));
                document.getElementById('radius-slider').value = Math.min(km, 30);
            }
        });

        document.getElementById('radius-slider').addEventListener('input', function (e) {
            const km = parseFloat(e.target.value);
            circle.setRadius(kmToMeters(km));
            document.getElementById('radius-input').value = km;
        });

        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('delivery-address-search'),
            {
                componentRestrictions: { country: 'gb' },
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

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key', 'YOUR_API_KEY') }}&libraries=places&callback=initDeliveryMap"
    async
    defer
></script>
@endpush