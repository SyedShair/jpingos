<?php

namespace App\Livewire\Deals;

use App\Models\Deal;
use App\Models\DealItem;
use App\Models\MenuItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Manager extends Component
{
    use WithFileUploads;

    // --- UI state ---
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $itemSearch = '';

    // --- Shared fields ---
    public string $type = 'flash_deal';
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;
    public ?int $usage_limit = null;

    /**
     * New image chosen in the form. Optional for every deal type — combo
     * and bundle deals fall back to a component dish's photo when this is
     * empty (see Deal::getImageUrlAttribute()), and the rest just show no
     * thumbnail.
     */
    public $photo = null;

    /** Existing image path, shown as a preview while editing. */
    public ?string $existingImage = null;

    // --- Discount fields ---
    public string $discount_type = 'percentage';
    public ?float $discount_value = null;
    public ?float $combo_price = null;
    public ?float $min_spend = null;

    // --- BOGO ---
    public ?int $buy_quantity = 1;
    public ?int $get_quantity = 1;
    public ?int $get_discount_percent = 100;

    // --- Promo code ---
    public ?string $promo_code = null;

    // --- Scheduling ---
    // Date and time are kept as SEPARATE plain-text fields on purpose.
    // Native <input type="time">/<input type="datetime-local"> render in
    // 12-hour AM/PM on many Windows/Chrome setups regardless of any HTML
    // attribute, because the browser follows the OS regional format, not
    // the page. Plain text with strict "H:i" validation sidesteps that
    // entirely — there's no AM/PM concept for the browser to render.
    public ?string $starts_at_date = null;
    public ?string $starts_at_time = null; // "HH:MM", 24-hour, validated
    public ?string $ends_at_date = null;
    public ?string $ends_at_time = null;   // "HH:MM", 24-hour, validated

    public array $recurring_days = [];
    public ?string $daily_start_time = null; // "HH:MM", 24-hour, validated
    public ?string $daily_end_time = null;   // "HH:MM", 24-hour, validated

    // --- Item associations ---
    public array $applyItemIds = [];
    public array $buyItemIds = [];
    public array $freeItemIds = [];
    public array $bundleComponents = [];

    public function render()
    {
        $deals = Deal::with('items.menuItem')->latest()->get()->groupBy(function ($deal) {
            foreach (Deal::typeGroups() as $groupName => $types) {
                if (array_key_exists($deal->type, $types)) {
                    return $groupName;
                }
            }
            return 'Other';
        });

        $menuItems = MenuItem::query()
            ->when($this->itemSearch, fn ($q) => $q->where('name', 'like', '%'.$this->itemSearch.'%'))
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('livewire.deals.manager', [
            'dealGroups' => $deals,
            'typeGroups' => Deal::typeGroups(),
            'menuItems'  => $menuItems,
        ]);
    }

    public function openCreate(?string $type = null): void
    {
        $this->resetForm();
        if ($type) {
            $this->type = $type;
        }
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $deal = Deal::with('items')->findOrFail($id);

        $this->editingId            = $deal->id;
        $this->type                 = $deal->type;
        $this->name                 = $deal->name;
        $this->description          = $deal->description;
        $this->is_active            = $deal->is_active;
        $this->usage_limit          = $deal->usage_limit;
        $this->discount_type        = $deal->discount_type;
        $this->discount_value       = $deal->discount_value ? (float) $deal->discount_value : null;
        $this->combo_price          = $deal->combo_price ? (float) $deal->combo_price : null;
        $this->min_spend            = $deal->min_spend ? (float) $deal->min_spend : null;
        $this->buy_quantity         = $deal->buy_quantity;
        $this->get_quantity         = $deal->get_quantity;
        $this->get_discount_percent = $deal->get_discount_percent;
        $this->promo_code           = $deal->promo_code;

        $this->existingImage = $deal->image;
        $this->photo         = null;

        $this->starts_at_date = $deal->starts_at?->format('Y-m-d');
        $this->starts_at_time = $deal->starts_at?->format('H:i');
        $this->ends_at_date   = $deal->ends_at?->format('Y-m-d');
        $this->ends_at_time   = $deal->ends_at?->format('H:i');

        $this->recurring_days   = $deal->recurring_days ?? [];
        $this->daily_start_time = $deal->daily_start_time ? substr($deal->daily_start_time, 0, 5) : null;
        $this->daily_end_time   = $deal->daily_end_time ? substr($deal->daily_end_time, 0, 5) : null;

        $this->applyItemIds = $deal->items->where('role', 'applies_to')->pluck('menu_item_id')->all();
        $this->buyItemIds   = $deal->items->where('role', 'buy')->pluck('menu_item_id')->all();
        $this->freeItemIds  = $deal->items->where('role', 'free')->pluck('menu_item_id')->all();

        $this->bundleComponents = $deal->items->where('role', 'bundle_component')->map(fn ($i) => [
            'menu_item_id'   => $i->menu_item_id,
            'quantity'       => $i->quantity,
            'override_price' => $i->override_price ? (float) $i->override_price : null,
        ])->values()->all();

        $this->showForm = true;
    }

    public function addBundleComponent(): void
    {
        $this->bundleComponents[] = ['menu_item_id' => null, 'quantity' => 1, 'override_price' => null];
    }

    public function removeBundleComponent(int $index): void
    {
        unset($this->bundleComponents[$index]);
        $this->bundleComponents = array_values($this->bundleComponents);
    }

    public function save(): void
    {
        $this->validate(array_merge([
            'name'  => 'required|string|max:150',
            'type'  => 'required|in:'.implode(',', array_keys(array_merge(...array_values(Deal::typeGroups())))),
            'photo' => 'nullable|image|max:2048',
        ], $this->rulesForType()));

        DB::transaction(function () {
            $startsAt = $this->starts_at_date
                ? Carbon::parse($this->starts_at_date.' '.($this->starts_at_time ?: '00:00'))
                : null;

            $endsAt = $this->ends_at_date
                ? Carbon::parse($this->ends_at_date.' '.($this->ends_at_time ?: '23:59'))
                : null;

            $data = [
                'name'                 => $this->name,
                'description'          => $this->description,
                'type'                 => $this->type,
                'discount_type'        => $this->discountTypeForCurrentDealType(),
                'discount_value'       => $this->discount_value,
                'combo_price'          => $this->combo_price,
                'min_spend'            => $this->min_spend,
                'buy_quantity'         => $this->buy_quantity,
                'get_quantity'         => $this->get_quantity,
                'get_discount_percent' => $this->get_discount_percent,
                'promo_code'           => $this->promo_code,
                'starts_at'            => $startsAt,
                'ends_at'              => $endsAt,
                'recurring_days'       => ! empty($this->recurring_days) ? array_map('intval', $this->recurring_days) : null,
                'daily_start_time'     => $this->daily_start_time ?: null,
                'daily_end_time'       => $this->daily_end_time ?: null,
                'usage_limit'          => $this->usage_limit,
                'is_active'            => $this->is_active,
            ];

            if ($this->photo) {
                $data['image'] = $this->photo->store('deals', 'public');
            }

            if ($this->editingId) {
                $deal = Deal::findOrFail($this->editingId);
                $oldImage = $deal->image;
                $deal->update($data);
                $deal->items()->delete();

                // Only remove the old file once the update has actually
                // succeeded, and only if a new image really replaced it —
                // otherwise a save with no new photo would wipe out the
                // existing one.
                if ($this->photo && $oldImage && $oldImage !== $deal->image) {
                    Storage::disk('public')->delete($oldImage);
                }
            } else {
                $deal = Deal::create($data);
            }

            $this->syncItems($deal);
        });

        session()->flash('status', $this->editingId ? 'Deal updated.' : 'Deal created.');
        $this->resetForm();
    }

    protected function syncItems(Deal $deal): void
    {
        foreach ($this->applyItemIds as $itemId) {
            DealItem::create(['deal_id' => $deal->id, 'menu_item_id' => $itemId, 'role' => 'applies_to']);
        }

        foreach ($this->buyItemIds as $itemId) {
            DealItem::create([
                'deal_id' => $deal->id, 'menu_item_id' => $itemId, 'role' => 'buy',
                'quantity' => $this->buy_quantity ?? 1,
            ]);
        }

        foreach ($this->freeItemIds as $itemId) {
            DealItem::create([
                'deal_id' => $deal->id, 'menu_item_id' => $itemId, 'role' => 'free',
                'quantity' => $this->get_quantity ?? 1,
            ]);
        }

        foreach ($this->bundleComponents as $component) {
            if (empty($component['menu_item_id'])) {
                continue;
            }
            DealItem::create([
                'deal_id'         => $deal->id,
                'menu_item_id'    => $component['menu_item_id'],
                'role'            => 'bundle_component',
                'quantity'        => $component['quantity'] ?? 1,
                'override_price'  => $component['override_price'] ?: null,
            ]);
        }
    }

    protected function rulesForType(): array
    {
        // "date_format:H:i" is Laravel's strict 24-hour time validator —
        // "16:00" passes, "4:00 PM" fails outright. This is the
        // server-side backstop matching the client-side pattern.
        $timeRule = 'nullable|date_format:H:i';

        return match ($this->type) {
            'flash_deal' => [
                'discount_type'   => 'required|in:percentage,fixed_amount',
                'discount_value'  => 'required|numeric|min:0',
                'applyItemIds'    => 'required|array|min:1',
                'starts_at_date'  => 'nullable|date',
                'starts_at_time'  => $timeRule,
                'ends_at_date'    => 'nullable|date',
                'ends_at_time'    => $timeRule,
            ],
            'happy_hour', 'lunch_special' => [
                'discount_type'     => 'required|in:percentage,fixed_amount',
                'discount_value'    => 'required|numeric|min:0',
                'applyItemIds'      => 'required|array|min:1',
                'daily_start_time'  => 'required|date_format:H:i',
                'daily_end_time'    => 'required|date_format:H:i|after:daily_start_time',
            ],
            'tiered_spend' => [
                'min_spend'      => 'required|numeric|min:0',
                'discount_type'  => 'required|in:fixed_amount,free_delivery',
                'discount_value' => 'required_if:discount_type,fixed_amount|nullable|numeric|min:0',
            ],
            'bogo' => [
                'buy_quantity'          => 'required|integer|min:1',
                'get_quantity'          => 'required|integer|min:1',
                'get_discount_percent'  => 'required|integer|min:1|max:100',
                'buyItemIds'            => 'required|array|min:1',
            ],
            'free_gift' => [
                'min_spend'   => 'required|numeric|min:0',
                'freeItemIds' => 'required|array|min:1',
            ],
            'combo', 'bundle' => [
                'combo_price'                    => 'required|numeric|min:0',
                'bundleComponents'                => 'required|array|min:2',
                'bundleComponents.*.menu_item_id' => 'required|exists:menu_items,id',
                'bundleComponents.*.quantity'     => 'required|integer|min:1',
            ],
            'promo_code' => [
                'promo_code'      => 'required|string|max:50|unique:deals,promo_code,'.($this->editingId ?? 'NULL'),
                'discount_type'   => 'required|in:percentage,fixed_amount',
                'discount_value'  => 'required|numeric|min:0',
            ],
            default => [],
        };
    }

    protected function discountTypeForCurrentDealType(): string
    {
        return match ($this->type) {
            'free_gift' => 'free_item',
            'combo', 'bundle' => 'fixed_price',
            'bogo' => 'percentage',
            default => $this->discount_type,
        };
    }

    public function delete(int $id): void
    {
        $deal = Deal::findOrFail($id);

        if ($deal->image) {
            Storage::disk('public')->delete($deal->image);
        }

        $deal->delete();
        session()->flash('status', 'Deal deleted.');
    }

    public function toggleActive(int $id): void
    {
        $deal = Deal::findOrFail($id);
        $deal->update(['is_active' => ! $deal->is_active]);
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'description', 'usage_limit',
            'photo', 'existingImage',
            'discount_value', 'combo_price', 'min_spend',
            'promo_code', 'recurring_days',
            'starts_at_date', 'starts_at_time', 'ends_at_date', 'ends_at_time',
            'daily_start_time', 'daily_end_time',
            'applyItemIds', 'buyItemIds', 'freeItemIds', 'bundleComponents',
            'showForm',
        ]);
        $this->type = 'flash_deal';
        $this->discount_type = 'percentage';
        $this->buy_quantity = 1;
        $this->get_quantity = 1;
        $this->get_discount_percent = 100;
        $this->is_active = true;
        $this->resetErrorBag();
    }
}