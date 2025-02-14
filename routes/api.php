<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Messages routes - protected with web auth
Route::middleware('auth')->group(function () {
    Route::get('/conversation/{conversationId}', [MessageController::class, 'getConversation'])
        ->where('conversationId', '[0-9]+');
    Route::post('/conversation/message/send', [MessageController::class, 'sendMessage']);

    Route::get('/messages/conversation/{recipientId}', [MessageController::class, 'findOrCreateConversation'])
        ->where('recipientId', '[0-9]+');
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
});
