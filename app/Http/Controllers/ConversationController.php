<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\CustomLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
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
            CustomLogger::error('Error in ConversationController@getConversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function findOrCreateDirectConversation(int $recipientId): JsonResponse
    {
        try {
            $userId = Auth::id();

            DB::beginTransaction();

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
                $conversation = Conversation::create([
                    'type' => 'direct'
                ]);
                $conversation->participants()->attach([$userId, $recipientId]);
            }

            DB::commit();

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
            CustomLogger::error('Error in ConversationController@findOrCreateDirectConversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $userId = Auth::id();

            DB::beginTransaction();

            // Find and verify user's access to conversation
            $conversation = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $userId))
                ->findOrFail($conversationId);

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
            CustomLogger::error('Error in ConversationController@sendMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
