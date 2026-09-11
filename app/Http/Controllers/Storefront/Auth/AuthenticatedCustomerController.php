<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedCustomerController extends Controller
{
    public function create()
    {
        return view('storefront.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'code'    => 401,
                'message' => 'Those credentials don\'t match our records.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'code'     => 200,
            'message'  => 'Login successful — redirecting...',
            'redirect' => session()->pull('url.intended', route('storefront.checkout')),
        ]);
    }

    public function destroy(Request $request)
    {
        
        Auth::guard('customer')->logout();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return redirect()->route('storefront.checkout');
    }
}