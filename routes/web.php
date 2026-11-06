<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectUrlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShortUrlAnalyticsController;

Route::view('/', 'home')->name('home');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/urls/{shortUrl}/analytics', ShortUrlAnalyticsController::class)->name('urls.analytics');
    Route::patch('/urls/{shortUrl}/toggle', [ShortUrlController::class, 'toggle'])->name('urls.toggle');
    Route::delete('/urls/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('urls.destroy');
});

Route::post('/urls', [ShortUrlController::class, 'store'])->name('urls.store');
Route::get('/s/{shortUrl:short_code}', RedirectUrlController::class)
    ->name('short.redirect');
    // 

    // This route is for redirecting short URLs to their original URLs. 
    //It uses the RedirectUrlController to handle the redirection logic. The {shortUrl:short_code} parameter allows the 
    //controller to find the corresponding ShortUrl model based on the provided short code.
Route::post('/urls', [ShortUrlController::class, 'store'])
    ->middleware('throttle:10,1, create-links') // Limit to 10 requests per minute for creating links
    ->name('urls.store');



// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
