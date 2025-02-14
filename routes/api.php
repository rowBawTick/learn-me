<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Conversation routes
    Route::get('/conversation/{conversationId}', [MessageController::class, 'getConversation'])
        ->where('conversationId', '[0-9]+');
    Route::post('/conversation/message/send', [MessageController::class, 'sendMessage']);

    // Direct messaging routes
    Route::get('/messages/{recipientId}', [MessageController::class, 'findOrCreateConversation'])
        ->where('recipientId', '[0-9]+');
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
});
