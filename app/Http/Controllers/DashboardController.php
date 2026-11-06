<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UrlClick;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
         $user = $request->user();

        $urls = $user->shortUrls()
            ->withCount('clicks')
            ->latest()
            ->paginate(10);

        $stats = [
            'links'  => $user->shortUrls()->count(),
            'clicks' => UrlClick::whereIn('short_url_id', $user->shortUrls()->select('id'))->count(),
            'active' => $user->shortUrls()->available()->count(),
        ];

        return view('dashboard', compact('urls', 'stats'));
    }
}
