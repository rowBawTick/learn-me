<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\CustomLogger;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();

            $conversations = Conversation::query()
                ->whereHas('participants', fn($query) => $query->where('user_id', $userId))
                ->with(['participants:id,name', 'lastMessage'])
                ->withCount(['messages as unread_count' => function ($query) use ($userId) {
                    $query->where('sender_id', '!=', $userId)
                        ->whereRaw('created_at > COALESCE((SELECT last_read_at FROM conversation_user WHERE conversation_id = conversations.id AND user_id = ?), ?)', [$userId, '1970-01-01']);
                }])
                ->orderByDesc(function ($query) {
                    $query->select('created_at')
                        ->from('messages')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->latest()
                        ->limit(1);
                })
                ->get();

            return Inertia::render('Messages/MessageInbox', [
                'conversations' => $conversations
            ]);

        } catch (\Exception $e) {
            CustomLogger::error('Error in MessageController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
