<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomerVerifyEmail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /** "Check your email" prompt shown right after registration. */
    public function notice()
    {
        $customer = Auth::guard('customer')->user();

        abort_unless($customer, 404);

        if ($customer->hasVerifiedEmail()) {
            return redirect()->route('storefront.home');
        }

        return view('storefront.auth.verify-notice', ['customer' => $customer]);
    }

    /** The signed link the customer clicks from their email. */
    public function verify(Request $request, Customer $customer)
    {
        if (! $request->hasValidSignature()) {
            return view('storefront.auth.verify-invalid');
        }

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
        }

        if (Auth::guard('customer')->id() !== $customer->id) {
            Auth::guard('customer')->login($customer);
        }

        return redirect()->back()->with('status', 'Your email is verified!');
    }

    /** AJAX resend — mirrors your reference's 15-second cooldown UX client-side. */
    public function resend(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer) {
            return response()->json(['code' => 400, 'message' => 'We couldn\'t find an account with that email.']);
        }

        if ($customer->hasVerifiedEmail()) {
            return response()->json(['code' => 400, 'message' => 'This account is already verified.']);
        }

        Mail::to($customer->email)->send(new CustomerVerifyEmail($customer));

        return response()->json(['code' => 200, 'message' => 'Verification email sent.']);
    }
}