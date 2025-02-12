<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function getConversation(User $recipient): JsonResponse
    {
        $userId = Auth::id();

        $messages = Message::query()
            ->where(function ($query) use ($recipient, $userId) {
                $query->where('sender_id', $userId)
                    ->where('recipient_id', $recipient->id);
            })
            ->orWhere(function ($query) use ($recipient, $userId) {
                $query->where('sender_id', $recipient->id)
                    ->where('recipient_id', $userId);
            })
            ->with(['sender:id,name', 'recipient:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, User $recipient): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $recipient->id,
            'message' => $validated['message'],
        ]);

        return response()->json($message->load(['sender:id,name', 'recipient:id,name']));
    }
}
