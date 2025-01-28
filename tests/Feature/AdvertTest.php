<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertTest extends TestCase
{
    use RefreshDatabase;

    public function test_advert_creation_with_currency_and_available_times(): void
    {
        $user = User::factory()->create(['is_tutor' => true]);
        $subject = Subject::factory()->create(['name' => 'Mathematics']);
        $currency = Currency::create([
            'code' => 'GBP',
            'symbol' => '£',
            'name' => 'British Pound Sterling'
        ]);

        $response = $this->actingAs($user)->post(route('adverts.store'), [
            'title' => 'Test Mathematics Tutoring',
            'subject_id' => $subject->id,
            'currency_id' => $currency->id,
            'price_per_hour' => 25.50,
            'description' => 'Professional mathematics tutoring for all levels',
            'available_times' => [
                [
                    'day_of_week' => 'Monday',
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_recurring' => true
                ],
                [
                    'day_of_week' => 'Wednesday',
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_recurring' => true
                ]
            ]
        ]);

        $response->assertRedirect(route('adverts.index'));
        
        $this->assertDatabaseHas('adverts', [
            'title' => 'Test Mathematics Tutoring',
            'currency_id' => $currency->id,
            'price_per_hour' => 25.50
        ]);

        $this->assertDatabaseHas('available_times', [
            'day_of_week' => 'Monday',
            'local_start_time' => '09:00',
            'local_end_time' => '17:00'
        ]);

        $this->assertDatabaseHas('available_times', [
            'day_of_week' => 'Wednesday',
            'local_start_time' => '09:00',
            'local_end_time' => '17:00'
        ]);
    }
}
