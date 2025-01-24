<?php

namespace Database\Seeders;

use App\Models\Advert;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvertSeeder extends Seeder
{
    public function run(): void
    {
        $tutors = User::factory(10)->create(['is_tutor' => true]);
        $subjects = Subject::all();

        // For each tutor, create 1-3 adverts for random subjects
        foreach ($tutors as $tutor) {
            $numberOfAdverts = rand(1, 3);
            $randomSubjects = $subjects->random($numberOfAdverts);

            foreach ($randomSubjects as $subject) {
                Advert::factory()->create([
                    'user_id' => $tutor->id,
                    'subject_id' => $subject->id,
                ]);
            }
        }
    }
}
