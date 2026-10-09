<div>
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Booking Events</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Booking Events</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <button type="button" class="btn btn-dark" wire:click="create">
                <i class="material-icons-outlined align-middle" style="font-size:16px;">add</i> New Event
            </button>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success">{{ session('status-message') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Event</th>
                            <th>Dates</th>
                            <th>Party size</th>
                            <th>Capacity</th>
                            <th>Booked</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            <tr wire:key="event-row-{{ $event->id }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($event->imageUrl())
                                            <img src="{{ $event->imageUrl() }}" width="40" height="40" class="rounded-2" style="object-fit:cover;" alt="">
                                        @endif
                                        <span class="fw-semibold">{{ $event->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $event->starts_on->format('j M') }} – {{ $event->ends_on->format('j M Y') }}</td>
                                <td>{{ $event->party_size_min }}–{{ $event->party_size_max }}</td>
                                <td>{{ $event->capacity ?? 'Unlimited' }}</td>
                                <td>{{ $event->bookings()->where('status', '!=', 'cancelled')->sum('party_size') }}</td>
                                <td>
                                    <button type="button" wire:click="toggleActive({{ $event->id }})"
                                        class="btn btn-sm {{ $event->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" wire:click="edit({{ $event->id }})" title="Edit">
                                        <i class="material-icons-outlined" style="font-size:16px;">edit</i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" wire:click="delete({{ $event->id }})"
                                        wire:confirm="Delete this event? Bookings already made for it will keep their record but lose the event link.">
                                        <i class="material-icons-outlined" style="font-size:16px;">delete</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No events yet. Create one for Halloween, Ramadan, or any special night.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($showForm)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="mb-0">{{ $editingId ? 'Edit event' : 'New event' }}</h5>
                        <button type="button" class="btn-close" wire:click="cancel"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Event name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Halloween Night">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Starts on</label>
                                <input type="date" class="form-control @error('starts_on') is-invalid @enderror" wire:model="starts_on">
                                @error('starts_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">Ends on</label>
                                <input type="date" class="form-control @error('ends_on') is-invalid @enderror" wire:model="ends_on">
                                @error('ends_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label class="form-label">Min party size</label>
                                <input type="number" min="1" class="form-control @error('party_size_min') is-invalid @enderror" wire:model="party_size_min">
                                @error('party_size_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Max party size</label>
                                <input type="number" min="1" class="form-control @error('party_size_max') is-invalid @enderror" wire:model="party_size_max">
                                @error('party_size_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-4">
                                <label class="form-label">Capacity (guests)</label>
                                <input type="number" min="1" class="form-control @error('capacity') is-invalid @enderror" wire:model="capacity" placeholder="Blank = unlimited">
                                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image (optional)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" wire:model="image" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="image" class="text-muted small mt-1">Uploading…</div>
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="mt-2 rounded-2" width="120" alt="">
                            @elseif ($existingImage)
                                <img src="{{ asset('storage/' . $existingImage) }}" class="mt-2 rounded-2" width="120" alt="">
                            @endif
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="event-active">
                            <label class="form-check-label" for="event-active">Show on the booking page</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                        <button type="button" class="btn btn-dark" wire:click="save" wire:loading.attr="disabled" wire:target="save,image">
                            {{ $editingId ? 'Save changes' : 'Create event' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
