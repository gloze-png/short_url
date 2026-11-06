<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class ShortUrlController extends Controller
{
    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $shortCode = $validated['custom_alias'] ?? $this->generateUniqueCode();

        $shortUrl = ShortUrl::create([
            'user_id'      => $request->user()?->id,
            'original_url' => $validated['original_url'],
            'short_code'   => $shortCode,
            'expires_at'   => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('home')
            ->with('status', 'Your short link is ready!')
            ->with('short_url', url('/s/' . $shortUrl->short_code));
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = Str::random(6); // Generate a random 5-character string
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }

    // Ensure the authenticated user is the owner of the short URL
    public function toggle(Request $request, ShortUrl $shortUrl): RedirectResponse
    {
        $this->ensureOwner($request, $shortUrl);

        $shortUrl->update(['is_active' => ! $shortUrl->is_active]);

        return back()->with('status', 'Link ' . ($shortUrl->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(Request $request, ShortUrl $shortUrl): RedirectResponse
    {
        $this->ensureOwner($request, $shortUrl);

        $shortUrl->delete();

        return back()->with('status', 'Link deleted.');
    }
    private function ensureOwner(Request $request, ShortUrl $shortUrl): void
    {
        abort_if($shortUrl->user_id !== $request->user()->id, 403);
    }
}
