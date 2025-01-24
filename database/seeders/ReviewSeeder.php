<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Advert;

class ReviewSeeder extends Seeder
{
    /**
     * Review types with their probability ranges and factory methods
     */
    private const REVIEW_TYPES = [
        ['max' => 70, 'type' => 'positive'],
        ['max' => 90, 'type' => 'moderate'],
        ['max' => 100, 'type' => 'negative'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adverts = Advert::all();
        $students = User::where('is_tutor', false)->get();

        foreach ($adverts as $advert) {
            $reviewers = $students->random(rand(1, 3));
            
            foreach ($reviewers as $reviewer) {
                $this->createReview($advert, $reviewer);
            }
        }
    }

    /**
     * Create a review with the given probability distribution
     */
    private function createReview(Advert $advert, User $reviewer): void
    {
        $random = rand(1, 100);
        
        $reviewType = collect(self::REVIEW_TYPES)
            ->first(fn($type) => $random <= $type['max'])['type'];

        Review::factory()
            ->state([
                'advert_id' => $advert->id,
                'reviewer_id' => $reviewer->id,
            ])
            ->{$reviewType}()
            ->create();
    }
}
