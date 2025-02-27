<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    /**
     * Store a new review for an advert.
     */
    public function store(Request $request, Advert $advert): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'description' => ['required', 'string'],
            ]);

            if ($advert->user_id === auth()->id()) {
                return response()->json([
                    'message' => 'You cannot review your own advert'
                ], 403);
            }

            $review = $advert->reviews()->create([
                'reviewer_id' => auth()->id(),
                'rating' => $validated['rating'],
                'description' => $validated['description'],
            ]);

            // Load relationships for the response
            $review->load(['reviewer', 'advert.subject', 'advert.user']);

            return response()->json([
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'description' => $review->description,
                    'reviewer_name' => $review->reviewer->name,
                    'tutor_name' => $review->advert->user->name,
                    'subject' => $review->advert->subject->name,
                    'created_at' => $review->created_at,
                ],
                'message' => 'Review submitted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating review: ' . $e->getMessage());
            return response()->json([
                'message' => config('app.debug') ? $e->getMessage() : 'Error creating review'
            ], 500);
        }
    }
}
