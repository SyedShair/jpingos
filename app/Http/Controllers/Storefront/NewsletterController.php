<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        // Honeypot: real visitors never see or fill the hidden "website"
        // field, bots usually do. Answer with the normal success message
        // and store nothing, so the bot has no signal to adapt to.
        if ($request->filled('website')) {
            return response()->json(['message' => 'Thanks for subscribing!']);
        }

        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
            'email.max'      => 'That email address is too long.',
        ]);

        $alreadySubscribed = response()->json([
            'message' => "You're already subscribed — thank you!",
        ]);

        try {
            $subscriber = NewsletterSubscriber::firstOrCreate(
                ['email' => $data['email']],
                [
                    'is_active'     => true,
                    'source'        => 'footer',
                    'ip_address'    => $request->ip(),
                    'subscribed_at' => now(),
                ]
            );
        } catch (UniqueConstraintViolationException $e) {
            // Two near-simultaneous submissions of the same address.
            return $alreadySubscribed;
        }

        if (! $subscriber->wasRecentlyCreated) {
            if ($subscriber->is_active) {
                return $alreadySubscribed;
            }

            // They unsubscribed earlier and are now signing up again by
            // their own choice, so re-activating is fine.
            $subscriber->update([
                'is_active'       => true,
                'ip_address'      => $request->ip(),
                'subscribed_at'   => now(),
                'unsubscribed_at' => null,
            ]);
        }

        return response()->json(['message' => 'Thanks for subscribing!']);
    }
}