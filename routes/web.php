<?php

use App\Http\Controllers\AdvertController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/search', function () {
    return Inertia::render('Search');
})->name('search');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Adverts
    Route::get('/adverts/search', [AdvertController::class, 'search'])->name('adverts.search');
    Route::get('/adverts/create', [AdvertController::class, 'create'])->name('adverts.create');
    Route::post('/adverts', [AdvertController::class, 'store'])->name('adverts.store');
    Route::get('/adverts/{advert}/edit', [AdvertController::class, 'edit'])->name('adverts.edit');
    Route::put('/adverts/{advert}', [AdvertController::class, 'update'])->name('adverts.update');
    Route::get('/adverts/{advert}', [AdvertController::class, 'show'])->name('adverts.show');
    Route::get('/my-adverts', function () {
        return Inertia::render('Listings/MyAdverts', [
            'adverts' => Auth::user()->adverts()
                ->with('subject')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    })->name('my-adverts');
    Route::patch('/adverts/{advert}/toggle-active', [AdvertController::class, 'toggleActive'])
        ->name('adverts.toggle-active');

    Route::get('/reviews/featured', [ReviewController::class, 'featured'])->name('reviews.featured');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/adverts/{advert}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/inbox', function () {
        return Inertia::render('Messages');
    })->name('messages');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
