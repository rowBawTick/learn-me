<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Get 3 random users to create conversations with
            $conversationPartners = $users->where('id', '!=', $user->id)
                                        ->random(min(3, $users->count() - 1));

            foreach ($conversationPartners as $partner) {
                // Create 3-5 messages for each conversation
                $messageCount = rand(3, 5);
                $now = now();

                for ($i = 0; $i < $messageCount; $i++) {
                    // Randomly decide sender and recipient
                    $isUserSender = rand(0, 1) === 1;
                    $sender = $isUserSender ? $user : $partner;
                    $recipient = $isUserSender ? $partner : $user;

                    Message::create([
                        'sender_id' => $sender->id,
                        'recipient_id' => $recipient->id,
                        'message' => $this->generateMessage(),
                        'created_at' => $now->addMinutes($i * 5), // Space messages 5 minutes apart
                        'updated_at' => $now->addMinutes($i * 5),
                    ]);
                }
            }
        }
    }

    private function generateMessage(): string
    {
        $messages = [
            "Hey, how's it going?\nI was wondering if you're free this weekend.",
            "Could we reschedule our lesson next week please? I'm going to be on holiday.",
            "Thanks for your help earlier!\nReally appreciate it.",
            "Did you see the latest updates?\nThey look amazing!",
            "Just checking in to see how you're doing.\nHope everything is well!",
            "Can we schedule a lesson sometime next week?\nI have a presentation to prepare for.",
            "Happy birthday! 🎉\nHope you have a fantastic day!"
        ];

        return $messages[array_rand($messages)];
    }
}
