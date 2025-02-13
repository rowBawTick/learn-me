<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function getConversation(int $recipientId): JsonResponse
    {
        try {
            $userId = Auth::id();

            $messages = Message::query()
                ->where(function ($query) use ($recipientId, $userId) {
                    $query->where('sender_id', $userId)
                        ->where('recipient_id', $recipientId);
                })
                ->orWhere(function ($query) use ($recipientId, $userId) {
                    $query->where('sender_id', $recipientId)
                        ->where('recipient_id', $userId);
                })
                ->with(['sender:id,name', 'recipient:id,name'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json($messages);
        } catch (\Exception $e) {
            Log::error('Error getting conversation', [
                'error' => $e->getMessage(),
                'recipientId' => $recipientId,
                'userId' => Auth::id()
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'message' => 'Error getting conversation',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

            return response()->json(['message' => 'Error getting conversation'], 500);
        }
    }

    public function store(Request $request, int $recipientId): JsonResponse
    {
        try {
            $userId = Auth::id();

            $validated = $request->validate([
                'message' => ['required', 'string', 'max:1000'],
            ]);

            $message = Message::create([
                'sender_id' => $userId,
                'recipient_id' => $recipientId,
                'message' => $validated['message'],
            ]);

            return response()->json($message->load(['sender:id,name', 'recipient:id,name']));
        } catch (\Exception $e) {
            Log::error('Error storing message', [
                'error' => $e->getMessage(),
                'recipientId' => $recipientId,
                'userId' => Auth::id(),
                'requestData' => $request->all()
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'message' => 'Error sending message',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }

            return response()->json(['message' => 'Error sending message'], 500);
        }
    }
}
