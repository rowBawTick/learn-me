<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Get a random selection of featured reviews (4-5 stars)
     */
    public function featured()
    {
        $reviews = Review::with(['advert.subject', 'advert.user', 'reviewer'])
            ->whereIn('rating', [4, 5])
            ->inRandomOrder()
            ->take(15)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'description' => $review->description,
                    'reviewer_name' => $review->reviewer->name,
                    'tutor_name' => $review->advert->user->name,
                    'subject' => $review->advert->subject->name,
                    'created_at' => $review->created_at,
                ];
            });

        return response()->json($reviews);
    }
}
