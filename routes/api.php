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

// Messages routes
Route::get('/messages/{recipient}', [MessageController::class, 'getConversation'])->where('recipient', '[0-9]+');
Route::post('/messages/{recipient}', [MessageController::class, 'store'])->where('recipient', '[0-9]+');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

});
