<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Languages
            'English',
            'Spanish',
            'French',
            'German',
            'Italian',
            'Portuguese',
            'Japanese',
            'Chinese (Mandarin)',
            'Korean',
            'Russian',
            'Arabic',
            'Dutch',
            'Swedish',
            'Vietnamese',
            'Thai',
            
            // Academic
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'History',
            'Geography',
            'Philosophy',
            'Psychology',
            
            // Arts & Music
            'Piano',
            'Guitar',
            'Violin',
            'Singing',
            'Painting',
            'Drawing',
            'Photography',
            
            // Professional
            'Public Speaking',
            'Business English',
            'Digital Marketing',
            'Web Development',
            'Data Science'
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(['name' => $subject]);
        }
    }
}
