<?php

namespace App\Services;

use App\Models\Advert;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertSearchService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Advert::query()
            ->with(['user', 'subject', 'currency'])
            ->select('adverts.*')
            ->join('users', 'users.id', '=', 'adverts.user_id')
            ->leftJoin('reviews', 'reviews.reviewer_id', '=', 'users.id')
            ->selectRaw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
            ->selectRaw('COUNT(reviews.id) as review_count')
            ->groupBy('adverts.id')
            ->where('is_active', true);

        // Apply subject filter
        if (!empty($filters['subject'])) {
            $query->whereHas('subject', function ($q) use ($filters) {
                $q->where('name', $filters['subject']);
            });
        }

        // Apply price filter
        if (!empty($filters['min_price'])) {
            $query->where('price_per_hour', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price_per_hour', '<=', $filters['max_price']);
        }

        // Sort by price (default)
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy('price_per_hour', $sortDirection);

        return $query->paginate(12);
    }
}
