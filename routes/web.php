<?php

use App\Http\Controllers\AdvertController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Models\Currency;
use App\Models\Subject;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::get('/my-adverts', function () {
        return Inertia::render('Listings/MyAdverts', [
            'adverts' => auth()->user()->adverts()->with('subject')->get()
        ]);
    })->name('adverts.index');

    Route::get('/adverts/create', function () {
        return Inertia::render('Listings/CreateAdvert', [
            'subjects' => Subject::all(['id', 'name']),
            'currencies' => Currency::all(['id', 'code', 'symbol', 'name'])
        ]);
    })->name('adverts.create');

    Route::post('/adverts', [AdvertController::class, 'store'])->name('adverts.store');
    Route::get('/adverts/{advert}', [AdvertController::class, 'show'])->name('adverts.show');

    Route::get('/reviews/featured', [ReviewController::class, 'featured'])->name('reviews.featured');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/messages', function () {
        return Inertia::render('Messages');
    })->name('messages');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
