<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomerResetPasswordEmail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('storefront.auth.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $customer = Customer::where('email', $request->email)->first();

        // Don't reveal whether the email exists — same response either
        // way, matching your reference's UX where the "check your email"
        // state always shows regardless.
        if ($customer) {
            Mail::to($customer->email)->send(new CustomerResetPasswordEmail($customer));
        }

        return response()->json([
            'code'    => 200,
            'message' => 'If an account exists for that email, a reset link has been sent.',
        ]);
    }
}