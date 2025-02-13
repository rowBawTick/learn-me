<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (if any)
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
});

// Messages routes - protected with web auth
Route::middleware('auth')->group(function () {
    Route::get('/messages/{recipientId}', [MessageController::class, 'getConversation'])->where('recipientId', '[0-9]+');
    Route::post('/messages/{recipientId}', [MessageController::class, 'store'])->where('recipientId', '[0-9]+');
});
