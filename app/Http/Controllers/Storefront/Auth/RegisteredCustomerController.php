<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredCustomerController extends Controller
{
    public function create()
    {
        return view('storefront.auth.register');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'first_name' => ['required', 'string', 'max:100'],
        'last_name'  => ['required', 'string', 'max:100'],
        'email'      => ['required', 'email', 'unique:customers,email'],
        'phone'      => ['nullable', 'string', 'max:20'],
        'password'   => ['required', 'confirmed', Password::min(8)],
    ]);

    $customer = Customer::create([
        ...$data,
        'password' => Hash::make($data['password']),
    ]);

    \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\CustomerVerifyEmail($customer));

    Auth::guard('customer')->login($customer);

    return response()->json([
        'code'     => 200,
        'message'  => 'Account created — check your email to verify.',
        'redirect' => route('storefront.verification.notice'),
    ]);
}
}