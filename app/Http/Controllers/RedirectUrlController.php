<?php

namespace App\Http\Controllers;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class RedirectUrlController extends Controller
{
    /**
     * Handle the incoming request.
     */

    public function __invoke(Request $request, ShortUrl $shortUrl): RedirectResponse
    {
        if (! $shortUrl->is_active) {
            abort(410, 'This link has been deactivated by its creator.');
        }

        if ($shortUrl->isExpired()) {
            abort(410, 'This link has expired.');
        }

        $shortUrl->clicks()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? '',
            'referer'    => $request->headers->get('referer')
                ? Str::limit($request->headers->get('referer'), 2048, '')
                : null,
            'clicked_at' => now(),
        ]);

        return redirect()->away($shortUrl->original_url);
    }
}

