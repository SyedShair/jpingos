<?php

namespace App\Livewire\Admin;

use App\Models\LoyaltyCoupon;
use App\Services\LoyaltyService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class LoyaltyCoupons extends Component
{
    use WithPagination;

    public string $search = '';

    /** '', 'active', 'used', 'expired' or 'voided'. */
    public string $statusFilter = '';

    /*
    |--------------------------------------------------------------------------
    | Create / edit form (one modal, used for both)
    |--------------------------------------------------------------------------
    */
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $editingCode = '';   // shown read-only while editing

    public string $email = '';
    public string $code = '';          // create only; blank = generate one
    public string $discount_type = 'percent';
    public string $discount_value = '10';
    public string $expires_at = '';
    public bool $send_email = true;

    protected $queryString = ['search', 'statusFilter'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create(): void
    {
        $this->resetForm();

        // Start from the same reward the automatic coupons give.
        $this->discount_type  = (string) config('loyalty.discount_type', 'percent');
        $this->discount_value = (string) config('loyalty.discount_value', 10);
        $this->expires_at     = now()->addDays((int) config('loyalty.expires_in_days', 60))->format('Y-m-d');
        $this->send_email     = true;

        $this->showForm = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |
    | A used coupon is history (an order references it), so it is read-only.
    | The code itself never changes after creation — it may already be in a
    | customer's inbox.
    |--------------------------------------------------------------------------
    */
    public function edit(int $id): void
    {
        $coupon = LoyaltyCoupon::findOrFail($id);

        if ($coupon->used_at) {
            session()->flash('status-message', "A used coupon can't be edited — it's kept as a record.");
            return;
        }

        $this->resetForm();

        $this->editingId      = $coupon->id;
        $this->editingCode    = $coupon->code;
        $this->email          = $coupon->email;
        $this->discount_type  = $coupon->discount_type;
        $this->discount_value = rtrim(rtrim(number_format((float) $coupon->discount_value, 2, '.', ''), '0'), '.');
        $this->expires_at     = $coupon->expires_at->format('Y-m-d');
        $this->send_email     = false;   // only re-email when the admin asks

        $this->showForm = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Save (create or update)
    |--------------------------------------------------------------------------
    */
    public function save(LoyaltyService $loyalty): void
    {
        $this->email = Str::lower(trim($this->email));
        $this->code  = Str::upper(trim($this->code));

        $rules = [
            'email'          => ['required', 'email:rfc', 'max:255'],
            'discount_type'  => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => [
                'required', 'numeric', 'gt:0',
                // A typo like "100" for a fixed £ amount is cheap to catch here.
                $this->discount_type === 'percent' ? 'max:100' : 'max:1000',
            ],
            'expires_at'     => ['required', 'date', 'after_or_equal:today'],
        ];

        if (! $this->editingId) {
            $rules['code'] = ['nullable', 'regex:/^[A-Z0-9-]+$/', 'max:20', Rule::unique('loyalty_coupons', 'code')];
        }

        $this->validate($rules, [
            'email.required'             => 'Enter the customer\'s email address.',
            'email.email'                => 'Enter a valid email address.',
            'discount_value.required'    => 'Enter the discount amount.',
            'discount_value.numeric'     => 'The discount must be a number.',
            'discount_value.gt'          => 'The discount must be more than zero.',
            'discount_value.max'         => $this->discount_type === 'percent'
                ? 'A percentage can\'t be more than 100.'
                : 'That amount looks too high — the limit is £1,000.',
            'expires_at.required'        => 'Choose an expiry date.',
            'expires_at.after_or_equal'  => 'Pick today or a later date.',
            'code.regex'                 => 'Use only letters, numbers and hyphens.',
            'code.unique'                => 'That code is already in use.',
        ]);

        $attributes = [
            'email'          => $this->email,
            'discount_type'  => $this->discount_type,
            'discount_value' => $this->discount_value,
            // A coupon "expiring on the 20th" is good for the whole of the 20th.
            'expires_at'     => Carbon::parse($this->expires_at)->endOfDay(),
        ];

        if ($this->editingId) {
            $coupon = LoyaltyCoupon::findOrFail($this->editingId);

            // Used while this form was open? Don't change a redeemed coupon.
            if ($coupon->used_at) {
                $this->closeForm();
                session()->flash('status-message', 'That coupon was just used, so it was not changed.');
                return;
            }

            $coupon->update($attributes);
            $message = 'Coupon ' . $coupon->code . ' updated.';
        } else {
            $coupon = LoyaltyCoupon::create($attributes + [
                'code' => $this->code !== '' ? $this->code : $loyalty->generateCode(),
            ]);

            $message = 'Coupon ' . $coupon->code . ' created.';
            $this->resetPage();
        }

        if ($this->send_email) {
            if ($coupon->status !== 'active') {
                $message .= " It wasn't emailed because the coupon isn't active.";
            } elseif ($loyalty->sendEmail($coupon)) {
                $message .= ' Emailed to ' . $coupon->email . '.';
            } else {
                $message .= ' The email could not be sent — see storage/logs/laravel.log.';
            }
        }

        $this->closeForm();
        session()->flash('status-message', $message);
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'editingCode', 'email', 'code', 'expires_at']);
        $this->discount_type  = 'percent';
        $this->discount_value = '10';
        $this->send_email     = true;
        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete / void / restore / resend
    |--------------------------------------------------------------------------
    */

    /**
     * Removes an unused coupon. Used coupons stay, because orders point at
     * them. Deleting does NOT hand the customer's progress back: the orders
     * that earned an automatic coupon stay "spent". To give someone a
     * replacement, create a new coupon.
     */
    public function delete(int $id): void
    {
        $deleted = LoyaltyCoupon::whereKey($id)->whereNull('used_at')->delete();

        session()->flash(
            'status-message',
            $deleted ? 'Coupon deleted.' : "A used coupon can't be deleted — it's kept as a record."
        );
    }

    /** Cancels an unused coupon but keeps it on file. */
    public function void(int $id): void
    {
        $changed = LoyaltyCoupon::whereKey($id)
            ->whereNull('used_at')
            ->whereNull('voided_at')
            ->update(['voided_at' => now()]);

        session()->flash('status-message', $changed ? 'Coupon voided.' : 'That coupon can no longer be voided.');
    }

    /** Undoes a void. (If it has since expired it will show as Expired until you edit its date.) */
    public function restore(int $id): void
    {
        $changed = LoyaltyCoupon::whereKey($id)
            ->whereNull('used_at')
            ->whereNotNull('voided_at')
            ->update(['voided_at' => null]);

        session()->flash('status-message', $changed ? 'Coupon restored.' : 'That coupon can\'t be restored.');
    }

    /** For when the original email never arrived. Only live coupons are worth re-sending. */
    public function resend(int $id, LoyaltyService $loyalty): void
    {
        $coupon = LoyaltyCoupon::findOrFail($id);

        if ($coupon->status !== 'active') {
            session()->flash('status-message', 'Only active coupons can be re-sent.');
            return;
        }

        session()->flash(
            'status-message',
            $loyalty->sendEmail($coupon)
                ? 'Coupon emailed to ' . $coupon->email . '.'
                : 'The email could not be sent — check your mail settings and storage/logs/laravel.log.'
        );
    }

    protected function filteredQuery()
    {
        return LoyaltyCoupon::query()
            ->with('usedOrder')
            ->when($this->search !== '', function ($q) {
                $term = '%' . addcslashes($this->search, '%_\\') . '%';
                $q->where(fn ($q) => $q->where('code', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->redeemable())
            ->when($this->statusFilter === 'used', fn ($q) => $q->whereNotNull('used_at'))
            ->when($this->statusFilter === 'voided', fn ($q) => $q->whereNull('used_at')->whereNotNull('voided_at'))
            ->when($this->statusFilter === 'expired', fn ($q) => $q->whereNull('used_at')->whereNull('voided_at')->where('expires_at', '<=', now()))
            ->latest('id');
    }

    public function render()
    {
        return view('livewire.admin.loyalty-coupons', [
            'coupons'     => $this->filteredQuery()->paginate(20),
            'issuedCount' => LoyaltyCoupon::count(),
            'activeCount' => LoyaltyCoupon::redeemable()->count(),
            'usedCount'   => LoyaltyCoupon::whereNotNull('used_at')->count(),
            'rule'        => (int) config('loyalty.orders_required', 5),
        ]);
    }
}