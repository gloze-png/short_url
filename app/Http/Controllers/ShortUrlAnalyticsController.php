<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;

class ShortUrlAnalyticsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ShortUrl $shortUrl)
    {
        abort_if($shortUrl->user_id !== $request->user()->id, 403);

        // Clicks per day for the last 14 days
        $byDate = $shortUrl->clicks()
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as total')
            ->where('clicked_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $counts = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $labels[] = $day;
            $counts[] = $byDate[$day] ?? 0;
        }

        // Top referrers
        $referrers = $shortUrl->clicks()
            ->selectRaw("COALESCE(referer, 'Direct / none') as source, COUNT(*) as total")
            ->groupBy('source')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent clicks
        $recentClicks = $shortUrl->clicks()
            ->latest('clicked_at')
            ->limit(20)
            ->get();

        return view('analytics', [
            'shortUrl'     => $shortUrl,
            'totalClicks'  => $shortUrl->clicks()->count(),
            'labels'       => $labels,
            'counts'       => $counts,
            'referrers'    => $referrers,
            'recentClicks' => $recentClicks,
        ]);
    }
}
