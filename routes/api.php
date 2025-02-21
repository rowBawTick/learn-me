<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\AdvertController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Adverts
    Route::patch('/adverts/{advert}/toggle-active', [AdvertController::class, 'toggleActive']);

    // Conversation routes
    Route::get('/conversation/{conversationId}', [ConversationController::class, 'getConversation'])
        ->where('conversationId', '[0-9]+');
    Route::post('/conversation/{conversationId}/message', [ConversationController::class, 'sendMessage'])
        ->where('conversationId', '[0-9]+');
    // Direct messaging routes
    Route::get('/messages/{recipientId}', [ConversationController::class, 'findOrCreateDirectConversation'])
        ->where('recipientId', '[0-9]+');
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
});
