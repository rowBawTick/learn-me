<?php

namespace Database\Seeders;

use App\Models\Advert;
use App\Models\AvailableTime;
use Illuminate\Database\Seeder;

class AvailableTimeSeeder extends Seeder
{
    public function run(): void
    {
        $adverts = Advert::all();

        // Common time slots that tutors might be available
        $timeSlots = [
            ['start' => '09:00', 'end' => '11:00'],
            ['start' => '11:00', 'end' => '13:00'],
            ['start' => '14:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '18:00'],
            ['start' => '18:00', 'end' => '20:00'],
        ];

        // Create 3-5 available time slots for each advert
        foreach ($adverts as $advert) {
            $numberOfSlots = rand(3, 5);
            // Keep track of used days to avoid duplicates for same advert
            $usedDays = [];
            for ($i = 0; $i < $numberOfSlots; $i++) {
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                // Remove already used days for this advert
                $days = array_diff($days, $usedDays);

                if (empty($days)) {
                    break; // All days are used
                }

                $day = array_rand(array_flip($days));
                $usedDays[] = $day;

                // Pick a random time slot
                $timeSlot = $timeSlots[array_rand($timeSlots)];

                AvailableTime::create([
                    'advert_id' => $advert->id,
                    'day_of_week' => $day,
                    'local_start_time' => $timeSlot['start'],
                    'local_end_time' => $timeSlot['end'],
                    'time_zone' => $advert->user->time_zone,
                    'is_recurring' => true,
                ]);
            }
        }
    }
}
