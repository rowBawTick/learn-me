<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\CustomLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function getConversation(int $conversationId): JsonResponse
    {
        try {
            $userId = Auth::id();

            $conversation = Conversation::with(['participants'])
                ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
                ->findOrFail($conversationId);

            // Update last_read_at for the current user
            $conversation->participants()->updateExistingPivot($userId, [
                'last_read_at' => now()
            ]);

            return response()->json([
                'messages' => $conversation->messages()
                    ->with('sender:id,name')
                    ->orderBy('created_at')
                    ->get()
            ]);

        } catch (\Exception $e) {
            CustomLogger::error('Error in MessageController@getConversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function findOrCreateConversation(int $recipientId): JsonResponse
    {
        try {
            $userId = Auth::id();

            // Find existing direct conversation between these users
            $conversation = Conversation::where('type', 'direct')
                ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
                ->whereHas('participants', fn($q) => $q->where('user_id', $recipientId))
                ->whereDoesntHave('participants', fn($q) => 
                    $q->whereNotIn('user_id', [$userId, $recipientId])
                )
                ->first();

            // If no conversation exists, create one
            if (!$conversation) {
                DB::beginTransaction();

                $conversation = Conversation::create([
                    'type' => 'direct'
                ]);
                $conversation->participants()->attach([$userId, $recipientId]);

                DB::commit();
            }

            // Load messages
            $messages = $conversation->messages()
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'conversation' => $conversation->load('participants:id,name'),
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            CustomLogger::error('Error in MessageController@findOrCreateConversation', [
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
