<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all tutors and students
        $tutors = User::where('is_tutor', true)->get();
        $students = User::where('is_tutor', false)->where('is_admin', false)->get();

        // Create conversations between students and tutors
        foreach ($students as $student) {
            // Each student messages 2-3 random tutors
            $selectedTutors = $tutors->random(min(rand(2, 3), $tutors->count()));

            foreach ($selectedTutors as $tutor) {
                DB::transaction(function () use ($student, $tutor) {
                    // Create a new conversation
                    $conversation = Conversation::create();
                    
                    // Attach both participants
                    $conversation->participants()->attach([
                        $student->id => ['created_at' => now(), 'updated_at' => now()],
                        $tutor->id => ['created_at' => now(), 'updated_at' => now()]
                    ]);

                    // Create 3-5 messages for each conversation
                    $messageCount = rand(3, 5);
                    $now = now();

                    // First message is always from student
                    $conversation->messages()->create([
                        'sender_id' => $student->id,
                        'message' => $this->generateInitialStudentMessage(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // Create subsequent messages
                    for ($i = 1; $i < $messageCount; $i++) {
                        $isStudentMessage = $i % 2 === 0; // Alternate between student and tutor
                        $sender = $isStudentMessage ? $student : $tutor;
                        $message = $isStudentMessage ? 
                            $this->generateStudentMessage() : 
                            $this->generateTutorMessage();

                        $conversation->messages()->create([
                            'sender_id' => $sender->id,
                            'message' => $message,
                            'created_at' => $now->addMinutes($i * 5), // Space messages 5 minutes apart
                            'updated_at' => $now->addMinutes($i * 5),
                        ]);
                    }
                });
            }
        }
    }

    private function generateInitialStudentMessage(): string
    {
        $messages = [
            "Hi! I saw your profile and I'm looking for help with my studies. Are you available for tutoring?",
            "Hello! I'm struggling with some concepts in your subject area. Would you be able to help?",
            "Hi there! I'm interested in booking some tutoring sessions. What's your availability like?",
            "Hello! I need help preparing for my upcoming exams. Are you taking new students?",
            "Hi! I'm looking for a tutor who can help me improve my understanding of this subject. Would you be available?",
        ];

        return $messages[array_rand($messages)];
    }

    private function generateStudentMessage(): string
    {
        $messages = [
            "That timing works perfectly for me!",
            "Could you explain that concept in more detail?",
            "I'm still having trouble understanding this particular topic.",
            "When would be a good time for our next session?",
            "Thank you, that explanation really helped!",
            "Would it be possible to focus on practice problems next time?",
            "I've completed the homework you assigned. Should I send it over?",
            "The last session was really helpful. Can we schedule another one?",
            "I have a few questions about the material we covered.",
            "Is there any additional reading you'd recommend?",
        ];

        return $messages[array_rand($messages)];
    }

    private function generateTutorMessage(): string
    {
        $messages = [
            "I'd be happy to help! What specific topics are you struggling with?",
            "Let's schedule a session to go through this in detail.",
            "Here's a helpful resource that explains this concept: [Example Link]",
            "Great progress! Let's focus on advanced topics next time.",
            "Would Tuesday at 3pm work for our next session?",
            "I've prepared some practice problems for you to work on.",
            "Don't worry, this is a common challenge. Let's break it down step by step.",
            "Yes, I'm available! Shall we start with an introductory session?",
            "That's excellent work! You're making great progress.",
            "Feel free to send over any specific questions before our next session.",
        ];

        return $messages[array_rand($messages)];
    }
}
