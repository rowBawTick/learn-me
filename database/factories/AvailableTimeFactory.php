<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\AvailableTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailableTime>
 */
class AvailableTimeFactory extends Factory
{
    protected $model = AvailableTime::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $startHour = fake()->numberBetween(7, 16); // random start times between 7am and 4pm
        $duration = fake()->numberBetween(1, 8); // Duration of availability in hours

        return [
            'advert_id' => Advert::factory(),
            'day_of_week' => fake()->randomElement($days),
            'local_start_time' => sprintf('%02d:00', $startHour),
            'local_end_time' => sprintf('%02d:00', $startHour + $duration),
            'time_zone' => fake()->randomElement([
                'Europe/London',
                'America/New_York',
                'Europe/Paris',
                'Asia/Tokyo',
                'Australia/Sydney'
            ]),
            'is_recurring' => true,
        ];
    }
}
