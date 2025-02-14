<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\CustomLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();

            // Get conversations grouped by the other participant
            $conversations = Message::query()
                ->where('sender_id', $userId)
                ->orWhere('recipient_id', $userId)
                ->with(['sender:id,name', 'recipient:id,name'])
                ->select([
                    DB::raw('CASE
                        WHEN sender_id = ' . $userId . ' THEN recipient_id
                        ELSE sender_id
                    END as participant_id'),
                    DB::raw('MAX(created_at) as last_message_at')
                ])
                ->groupBy('participant_id')
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($conversation) use ($userId) {
                    // Get the last message for this conversation
                    $lastMessage = Message::query()
                        ->where(function ($query) use ($userId, $conversation) {
                            $query->where('sender_id', $userId)
                                ->where('recipient_id', $conversation->participant_id);
                        })
                        ->orWhere(function ($query) use ($userId, $conversation) {
                            $query->where('sender_id', $conversation->participant_id)
                                ->where('recipient_id', $userId);
                        })
                        ->with(['sender:id,name', 'recipient:id,name'])
                        ->latest()
                        ->first();

                    return [
                        'participant_id' => $conversation->participant_id,
                        'participant_name' => $lastMessage->sender_id === $userId
                            ? $lastMessage->recipient->name
                            : $lastMessage->sender->name,
                        'last_message' => $lastMessage->message,
                        'last_message_at' => $lastMessage->created_at,
                        'is_sender' => $lastMessage->sender_id === $userId,
                    ];
                });

            return Inertia::render('Messages/MessageInbox', [
                'conversations' => $conversations
            ]);
        } catch (\Exception $e) {
            return CustomLogger::errorResponse(
                'Error fetching conversations',
                $e,
                ['userId' => $userId]
            );
        }
    }
}
