<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Category;

class CustomerAccountController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $orders = Order::where('customer_id', $customer->id)
            ->latest()
            ->get();

 $categories = Category::topLevel()
            ->active()
            ->ordered()
            ->with([
                'children' => fn ($q) => $q->active()->ordered()
            ])
            ->get();
        return view('storefront.account', [
            'customer' => $customer,
            'orders'   => $orders,
            'categories' => $categories,
        ]);
    }

    public function updateDetails(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone'      => ['required', 'string', 'max:20'],
        ]);

        $customer->update($data);

        return response()->json([
            'code'    => 200,
            'message' => 'Your details have been updated.',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'current' => ['required', 'string'],
            'new'     => ['required', 'string', 'min:8'],
            'confirm' => ['required', 'string', 'same:new'],
        ]);

        if (! Hash::check($data['current'], $customer->password)) {
            return response()->json([
                'code'    => 201,
                'message' => 'Your current password is incorrect.',
            ]);
        }

        // Customer casts 'password' => 'hashed', so assigning the plain
        // string here is hashed automatically on save — no manual
        // Hash::make() needed.
        $customer->update(['password' => $data['new']]);

        return response()->json([
            'code'    => 200,
            'message' => 'Your password has been changed.',
        ]);
    }

    public function updateAddress(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // NOTE: the reference page this was adapted from used one combined
        // "shipping_address" text field. Your Customer model has separate
        // address/apartment/city/postcode columns instead, so this form
        // collects those individually rather than matching the reference
        // 1:1 — flagging the deviation rather than silently picking one.
        $data = $request->validate([
            'address'   => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:100'],
            'postcode'  => ['nullable', 'string', 'max:20'],
            'phone'     => ['required', 'string', 'max:20'],
        ]);

        $customer->update($data);

        return response()->json([
            'code'    => 200,
            'message' => 'Your address has been updated.',
        ]);
    }
}