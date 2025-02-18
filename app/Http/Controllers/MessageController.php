<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\CustomLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(): Response
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

    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'recipient_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000',
            ]);

            $userId = Auth::id();

            DB::beginTransaction();

            // Find or create conversation between these users
            $conversation = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $userId))
                ->whereHas('participants', fn($q) => $q->where('user_id', $validated['recipient_id']))
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create();
                $conversation->participants()->attach([$userId, $validated['recipient_id']]);
            }

            $message = $conversation->messages()->create([
                'sender_id' => $userId,
                'message' => $validated['message'],
            ]);

            DB::commit();

            return response()->json([
                'message' => $message->load('sender:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            CustomLogger::error('Error in MessageController@sendMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
