<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function create(Request $request, Customer $customer)
    {
        if (! $request->hasValidSignature()) {
            return view('storefront.auth.reset-invalid');
        }

        return view('storefront.auth.reset-password', [
            'customer' => $customer,
            'signedUrl' => $request->fullUrl(),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['code' => 400, 'message' => 'This reset link has expired — please request a new one.']);
        }

        $data = $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer->update(['password' => Hash::make($data['new_password'])]);

        Auth::guard('customer')->login($customer);

        return response()->json([
            'code'     => 200,
            'message'  => 'Password updated — you\'re now logged in.',
            'redirect' => route('storefront.home'),
        ]);
    }
}