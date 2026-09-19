<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Manager extends Component
{
    use WithFileUploads;

    public string $siteName = '';
    public ?string $existingLogo = null;
    public ?string $existingBannerOne = null;
    public ?string $existingBannerTwo = null;

    // Newly-selected files (nullable — only set when the user picks a
    // new one; leaving these null on save means "keep the existing file").
    public $newLogo = null;
    public $newBannerOne = null;
    public $newBannerTwo = null;

    public string $vatNumber = '';
    public string $email = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $instagram = '';
    public string $facebook = '';

    public array $openingHours = [];
    public bool $showDeliveryChecker = true;

    protected function rules(): array
    {
        return [
            'siteName'    => ['nullable', 'string', 'max:150'],
            'newLogo'     => ['nullable', 'image', 'max:2048'],
            'newBannerOne' => ['nullable', 'image', 'max:4096'],
            'newBannerTwo' => ['nullable', 'image', 'max:4096'],
            'vatNumber'   => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'string', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'whatsapp'    => ['nullable', 'string', 'max:30'],
            'instagram'   => ['nullable', 'string', 'max:255'],
            'facebook'    => ['nullable', 'string', 'max:255'],
            'showDeliveryChecker' => ['boolean'],

            'openingHours.*.open'   => ['required_unless:openingHours.*.closed,true', 'nullable', 'date_format:H:i'],
            'openingHours.*.close'  => ['required_unless:openingHours.*.closed,true', 'nullable', 'date_format:H:i'],
            'openingHours.*.closed' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        $setting = Setting::current();

        $this->siteName   = $setting->site_name ?? '';
        $this->vatNumber  = $setting->vat_number ?? '';
        $this->email      = $setting->email ?? '';
        $this->phone      = $setting->phone ?? '';
        $this->whatsapp   = $setting->whatsapp ?? '';
        $this->instagram  = $setting->instagram ?? '';
        $this->facebook   = $setting->facebook ?? '';
        $this->showDeliveryChecker = $setting->show_delivery_checker;

        $this->existingLogo      = $setting->logo;
        $this->existingBannerOne = $setting->banner_one;
        $this->existingBannerTwo = $setting->banner_two;

        // Fall back to the same defaults current() seeds a brand-new row
        // with, in case an existing row somehow has a null/partial
        // opening_hours value.
        $this->openingHours = $setting->opening_hours ?? collect(Setting::DAYS)->mapWithKeys(fn ($day) => [
            $day => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
        ])->all();
    }

    public function save(): void
    {
        $this->validate();

        $setting = Setting::current();

        $data = [
            'site_name'  => $this->siteName ?: null,
            'vat_number' => $this->vatNumber ?: null,
            'email'      => $this->email ?: null,
            'phone'      => $this->phone ?: null,
            'whatsapp'   => $this->whatsapp ?: null,
            'instagram'  => $this->instagram ?: null,
            'facebook'   => $this->facebook ?: null,
            'opening_hours' => $this->openingHours,
            'show_delivery_checker' => $this->showDeliveryChecker,
        ];

        if ($this->newLogo) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }
            $data['logo'] = $this->newLogo->store('settings', 'public');
        }

        if ($this->newBannerOne) {
            if ($setting->banner_one) {
                Storage::disk('public')->delete($setting->banner_one);
            }
            $data['banner_one'] = $this->newBannerOne->store('settings', 'public');
        }

        if ($this->newBannerTwo) {
            if ($setting->banner_two) {
                Storage::disk('public')->delete($setting->banner_two);
            }
            $data['banner_two'] = $this->newBannerTwo->store('settings', 'public');
        }

        $setting->update($data);

        // Refresh the "existing" preview paths and clear the pending
        // uploads so the form doesn't re-submit the same file if saved
        // again, and the newly-uploaded image shows in its preview slot.
        $this->existingLogo      = $setting->logo;
        $this->existingBannerOne = $setting->banner_one;
        $this->existingBannerTwo = $setting->banner_two;
        $this->newLogo = null;
        $this->newBannerOne = null;
        $this->newBannerTwo = null;

        session()->flash('status-message', 'Settings saved.');
    }

    public function render()
    {
        return view('livewire.settings.manager', [
            'days' => Setting::DAYS,
        ]);
    }
}