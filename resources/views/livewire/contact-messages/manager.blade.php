<div>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Contact Messages</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Messages</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success" wire:key="status-flash-{{ now()->timestamp }}">
            {{ session('status-message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="row g-3 mb-3">
                <div class="col-auto flex-grow-1">
                    <div class="position-relative">
                        <input
                            class="form-control px-5"
                            type="search"
                            placeholder="Search by name, email, or subject..."
                            wire:model.live.debounce.400ms="search"
                        >
                        <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">All statuses</option>
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="replied">Replied</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            <tr wire:key="contact-row-{{ $message->id }}" class="{{ $message->status === 'new' ? 'fw-bold' : '' }}">
                                <td>
                                    {{ $message->name }}
                                    <br>
                                    <span class="text-muted small fw-normal">{{ $message->email }}</span>
                                </td>
                                <td>{{ $message->subject }}</td>
                                <td>
                                    @php
                                        $statusColors = ['new' => 'primary', 'read' => 'secondary', 'replied' => 'success'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$message->status] ?? 'secondary' }} text-capitalize">
                                        {{ $message->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small fw-normal">{{ $message->created_at->format('d M, g:i A') }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" wire:click="viewMessage({{ $message->id }})" title="View">
                                        <i class="material-icons-outlined" style="font-size: 16px;">visibility</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No messages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $messages->links() }}
            </div>

        </div>
    </div>

    {{-- =================================================
         MESSAGE DETAIL + REPLY MODAL
    ================================================== --}}

    @if ($selectedMessage)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="contact-detail-{{ $selectedMessage->id }}">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $selectedMessage->subject }}</h5>
                            <p class="mb-0 text-muted small">
                                From {{ $selectedMessage->name }} ({{ $selectedMessage->email }}) —
                                {{ $selectedMessage->created_at->format('d M Y, g:i A') }}
                            </p>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetail"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-2">Message</h6>
                            <p class="mb-0" style="white-space: pre-line;">{{ $selectedMessage->message }}</p>
                        </div>

                        @if ($selectedMessage->admin_reply)
                            <div class="mb-4 p-3" style="background:#f0f9f4; border:1px solid #cdeadb; border-radius:8px;">
                                <h6 class="fw-bold text-uppercase small mb-2" style="color:#146c40;">
                                    Your Reply — {{ $selectedMessage->replied_at?->format('d M Y, g:i A') }}
                                </h6>
                                <p class="mb-0" style="white-space: pre-line;">{{ $selectedMessage->admin_reply }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="form-label fw-bold">
                                {{ $selectedMessage->admin_reply ? 'Send another reply' : 'Reply' }}
                            </label>
                            <textarea class="form-control" rows="5" wire:model="replyText" placeholder="Type your reply — this will be emailed to {{ $selectedMessage->email }}"></textarea>
                            @error('replyText') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetail">Close</button>
                        <button type="button" class="btn btn-dark" wire:click="sendReply" wire:loading.attr="disabled" wire:target="sendReply">
                            <span wire:loading.remove wire:target="sendReply">Send Reply</span>
                            <span wire:loading wire:target="sendReply">Sending...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>