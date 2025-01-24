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

                AvailableTime::factory()->create([
                    'advert_id' => $advert->id,
                    'day_of_week' => $day,
                ]);
            }
        }
    }
}
