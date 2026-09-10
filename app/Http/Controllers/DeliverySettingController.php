<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DeliverySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliverySettingController extends Controller
{
    public function edit(): View
    {
        return view('delivery-settings.edit', [
            'setting' => DeliverySetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address'   => ['nullable', 'string', 'max:255'],
            'postcode'  => ['nullable', 'string', 'max:12'],
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_km' => ['required', 'numeric', 'min:0.5', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        DeliverySetting::current()->update($data);

        return back()->with('status', 'Delivery area updated.');
    }
}