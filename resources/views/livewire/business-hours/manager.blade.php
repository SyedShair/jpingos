<div>
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Opening Hours</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Opening Hours</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status-message'))
        <div class="alert alert-success">{{ session('status-message') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-4">
                Set when you're open each day. The booking page only lets customers pick a date and time inside these hours.
                For a night that runs past midnight — e.g. open 6pm, close 1am — tick <strong>"Closes after midnight"</strong>.
            </p>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Day</th>
                            <th>Closed</th>
                            <th>Opens</th>
                            <th>Closes</th>
                            <th>Closes after midnight</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dayNames as $dow => $name)
                            <tr wire:key="day-{{ $dow }}">
                                <td class="fw-semibold">{{ $name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="days.{{ $dow }}.closed">
                                    </div>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" style="max-width:130px;"
                                        wire:model="days.{{ $dow }}.opens" @if ($days[$dow]['closed']) disabled @endif>
                                    @error("days.{$dow}.opens") <div class="text-danger small">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" style="max-width:130px;"
                                        wire:model="days.{{ $dow }}.closes" @if ($days[$dow]['closed']) disabled @endif>
                                    @error("days.{$dow}.closes") <div class="text-danger small">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="days.{{ $dow }}.closes_next_day" @if ($days[$dow]['closed']) disabled @endif>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="copyToAll({{ $dow }})" title="Copy these hours to every day">
                                        Copy to all
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-dark" wire:click="save" wire:loading.attr="disabled">
                Save opening hours
            </button>
        </div>
    </div>
</div>
