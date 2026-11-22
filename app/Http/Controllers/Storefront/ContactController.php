<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Models\Category;
class ContactController extends Controller
{
    public function index()
    {
         $categories = Category::topLevel()
            ->active()
            ->ordered()
            ->with([
                'children' => fn ($q) => $q->active()->ordered()
            ])
            ->get();
        return view('storefront.contact', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create($data);

        return response()->json([
            'message' => 'Thanks — your message has been sent. We\'ll get back to you soon.',
        ]);
    }
}