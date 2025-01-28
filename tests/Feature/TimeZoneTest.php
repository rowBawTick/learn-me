<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Advert;
use App\Models\Subject;
use App\Models\AvailableTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeZoneTest extends TestCase
{
    use RefreshDatabase;

    private const TUTOR_START_TIME = '09:00';
    private const TUTOR_END_TIME = '17:00';

    protected function setUp(): void
    {
        parent::setUp();
        Subject::create(['name' => 'Mathematics']);
    }

    /**
     * Helper function to create a tutor with availability
     */
    private function createTutorWithAvailability(string $timezone): array
    {
        $tutor = User::factory()->tutor()->create(['time_zone' => $timezone]);
        $advert = Advert::factory()->create(['user_id' => $tutor->id]);
        
        $availableTime = AvailableTime::create([
            'advert_id' => $advert->id,
            'day_of_week' => 'Monday',
            'local_start_time' => self::TUTOR_START_TIME,
            'local_end_time' => self::TUTOR_END_TIME,
            'time_zone' => $timezone,
            'is_recurring' => true,
        ]);

        return compact('tutor', 'advert', 'availableTime');
    }

    public function test_tutor_availability_remains_consistent_during_dst_change(): void
    {
        // Create a UK tutor who sets their availability for 9 AM - 5 PM
        ['availableTime' => $availableTime] = $this->createTutorWithAvailability('Europe/London');

        // Test winter time (GMT) - A date in January when both UK and US are in standard time
        $winterDate = Carbon::create(2024, 1, 15, 0, 0, 0, 'Europe/London');
        Carbon::setTestNow($winterDate);

        // During standard time, London is 5 hours ahead of New York
        $expectedStart = Carbon::parse(self::TUTOR_START_TIME, 'Europe/London')
            ->subHours(5)
            ->format('H:i');
        $expectedEnd = Carbon::parse(self::TUTOR_END_TIME, 'Europe/London')
            ->subHours(5)
            ->format('H:i');

        $this->assertEquals($expectedStart, $availableTime->getStartTimeInTimeZone('America/New_York'));
        $this->assertEquals($expectedEnd, $availableTime->getEndTimeInTimeZone('America/New_York'));

        // Test summer time (BST) - A date in July when both UK and US are in daylight time
        $summerDate = Carbon::create(2024, 7, 15, 0, 0, 0, 'Europe/London');
        Carbon::setTestNow($summerDate);

        // During summer time, London is still 5 hours ahead of New York
        // The absolute UTC offset changes for both zones, but the relative difference stays the same
        $this->assertEquals($expectedStart, $availableTime->getStartTimeInTimeZone('America/New_York'));
        $this->assertEquals($expectedEnd, $availableTime->getEndTimeInTimeZone('America/New_York'));

        // Verify that the tutor's local time remains constant
        $this->assertEquals(self::TUTOR_START_TIME, $availableTime->local_start_time);
        $this->assertEquals(self::TUTOR_END_TIME, $availableTime->local_end_time);

        Carbon::setTestNow(); // Reset time
    }

    public function test_availability_during_dst_transition_periods(): void
    {
        // Create a UK tutor who sets their availability for 9 AM - 5 PM
        ['availableTime' => $availableTime] = $this->createTutorWithAvailability('Europe/London');

        // Test the period when UK has switched to BST but US is still in standard time
        // UK moves to BST on March 31, 2024, while US moved to EDT on March 10, 2024
        $transitionDate = Carbon::create(2024, 3, 15, 0, 0, 0, 'Europe/London');
        Carbon::setTestNow($transitionDate);

        // During this period, London is 4 hours ahead of New York
        $expectedStart = Carbon::parse(self::TUTOR_START_TIME, 'Europe/London')
            ->subHours(4)
            ->format('H:i');
        $expectedEnd = Carbon::parse(self::TUTOR_END_TIME, 'Europe/London')
            ->subHours(4)
            ->format('H:i');

        $this->assertEquals($expectedStart, $availableTime->getStartTimeInTimeZone('America/New_York'));
        $this->assertEquals($expectedEnd, $availableTime->getEndTimeInTimeZone('America/New_York'));

        // Verify tutor's local time remains constant
        $this->assertEquals(self::TUTOR_START_TIME, $availableTime->local_start_time);
        $this->assertEquals(self::TUTOR_END_TIME, $availableTime->local_end_time);

        Carbon::setTestNow(); // Reset time
    }
}
