<div>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Settings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success" wire:key="status-flash-{{ now()->timestamp }}">
            {{ session('status-message') }}
        </div>
    @endif

    <form wire:submit="save">

        <div class="row">

            {{-- =================================================
                 GENERAL
            ================================================== --}}

            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">General</h5>

                        <div class="mb-3">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" wire:model="siteName">
                            @error('siteName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">VAT Number</label>
                            <input type="text" class="form-control" wire:model="vatNumber">
                            @error('vatNumber') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telephone</label>
                                <input type="text" class="form-control" wire:model="phone">
                                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" wire:model="whatsapp">
                                @error('whatsapp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instagram URL</label>
                                <input type="text" class="form-control" wire:model="instagram" placeholder="https://instagram.com/...">
                                @error('instagram') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook URL</label>
                                <input type="text" class="form-control" wire:model="facebook" placeholder="https://facebook.com/...">
                                @error('facebook') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- =================================================
                     OPENING HOURS
                ================================================== --}}

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Opening Hours</h5>

                        @foreach ($days as $day)
                            <div class="row align-items-center mb-2" wire:key="hours-{{ $day }}">
                                <div class="col-3 col-md-2 text-capitalize fw-semibold">
                                    {{ $day }}
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            wire:model="openingHours.{{ $day }}.closed"
                                            id="closed-{{ $day }}"
                                        >
                                        <label class="form-check-label" for="closed-{{ $day }}">Closed</label>
                                    </div>
                                </div>
                                <div class="col-3 col-md-4">
                                    <input
                                        type="time"
                                        class="form-control"
                                        wire:model="openingHours.{{ $day }}.open"
                                        @disabled($openingHours[$day]['closed'] ?? false)
                                    >
                                </div>
                                <div class="col-3 col-md-4">
                                    <input
                                        type="time"
                                        class="form-control"
                                        wire:model="openingHours.{{ $day }}.close"
                                        @disabled($openingHours[$day]['closed'] ?? false)
                                    >
                                </div>
                            </div>
                        @endforeach

                        @error('openingHours') <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                    </div>
                </div>
            </div>

            {{-- =================================================
                 LOGO / BANNERS / TOGGLES
            ================================================== --}}

            <div class="col-12 col-lg-4">

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Logo</h5>

                        @if ($existingLogo && ! $newLogo)
                            <img src="{{ Storage::disk('public')->url($existingLogo) }}" class="img-fluid rounded mb-3" style="max-height: 100px;">
                        @endif

                        @if ($newLogo)
                            <img src="{{ $newLogo->temporaryUrl() }}" class="img-fluid rounded mb-3" style="max-height: 100px;">
                        @endif

                        <input type="file" class="form-control" wire:model="newLogo" accept="image/*">
                        @error('newLogo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="newLogo" class="small text-muted mt-1">Uploading...</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Banner 1</h5>

                        @if ($existingBannerOne && ! $newBannerOne)
                            <img src="{{ Storage::disk('public')->url($existingBannerOne) }}" class="img-fluid rounded mb-3">
                        @endif

                        @if ($newBannerOne)
                            <img src="{{ $newBannerOne->temporaryUrl() }}" class="img-fluid rounded mb-3">
                        @endif

                        <input type="file" class="form-control" wire:model="newBannerOne" accept="image/*">
                        @error('newBannerOne') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="newBannerOne" class="small text-muted mt-1">Uploading...</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Banner 2</h5>

                        @if ($existingBannerTwo && ! $newBannerTwo)
                            <img src="{{ Storage::disk('public')->url($existingBannerTwo) }}" class="img-fluid rounded mb-3">
                        @endif

                        @if ($newBannerTwo)
                            <img src="{{ $newBannerTwo->temporaryUrl() }}" class="img-fluid rounded mb-3">
                        @endif

                        <input type="file" class="form-control" wire:model="newBannerTwo" accept="image/*">
                        @error('newBannerTwo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="newBannerTwo" class="small text-muted mt-1">Uploading...</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Storefront</h5>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="show-delivery-checker" wire:model="showDeliveryChecker">
                            <label class="form-check-label" for="show-delivery-checker">
                                Show delivery-area checker on checkout
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save Settings</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>

            </div>

        </div>

    </form>

</div>