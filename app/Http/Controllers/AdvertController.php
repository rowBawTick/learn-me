<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Currency;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\AdvertSearchService;

class AdvertController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Adverts/MyAdverts', [
            'adverts' => auth()->user()->adverts()->with(['subject', 'currency'])->get()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Listings/AdvertForm', [
            'subjects' => Subject::all(['id', 'name']),
            'currencies' => Currency::all(['id', 'code', 'symbol', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'currency_id' => 'required|exists:currencies,id',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'required|string',
            'available_times' => 'required|array|min:1',
            'available_times.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'available_times.*.start_time' => 'required|date_format:H:i',
            'available_times.*.end_time' => 'required|date_format:H:i|after:available_times.*.start_time',
        ]);

        $advert = Advert::create([
            'user_id' => auth()->id(),
            'subject_id' => $validated['subject_id'],
            'currency_id' => $validated['currency_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price_per_hour' => $validated['price_per_hour'],
            'is_active' => true,
        ]);

        $userTimeZone = auth()->user()->time_zone;

        foreach ($validated['available_times'] as $timeSlot) {
            $advert->availableTimes()->create([
                'day_of_week' => $timeSlot['day_of_week'],
                'local_start_time' => $timeSlot['start_time'],
                'local_end_time' => $timeSlot['end_time'],
                'time_zone' => $userTimeZone,
                'is_recurring' => true,
            ]);
        }

        return redirect()->route('my-adverts');
    }

    public function show(Advert $advert): Response
    {
        $advert->load([
            'subject',
            'currency',
            'availableTimes',
            'user',
            'reviews' => fn($query) => $query->with('reviewer')->latest()
        ]);

        return Inertia::render('Adverts/ShowAdvert', [
            'advert' => $advert
        ]);
    }

    public function edit(Advert $advert): Response
    {
        if ($advert->user_id !== auth()->id()) {
            abort(403);
        }

        $advert->load('availableTimes');

        return Inertia::render('Listings/AdvertForm', [
            'advert' => $advert,
            'subjects' => Subject::all(['id', 'name']),
            'currencies' => Currency::all(['id', 'code', 'symbol', 'name'])
        ]);
    }

    public function update(Request $request, Advert $advert)
    {
        if ($advert->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'currency_id' => 'required|exists:currencies,id',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'required|string',
            'available_times' => 'required|array|min:1',
            'available_times.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'available_times.*.start_time' => 'required|date_format:H:i',
            'available_times.*.end_time' => 'required|date_format:H:i|after:available_times.*.start_time',
        ]);

        $advert->update([
            'subject_id' => $validated['subject_id'],
            'currency_id' => $validated['currency_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price_per_hour' => $validated['price_per_hour'],
        ]);

        // Delete existing time slots and create new ones
        $advert->availableTimes()->delete();

        $userTimeZone = auth()->user()->time_zone;

        foreach ($validated['available_times'] as $timeSlot) {
            $advert->availableTimes()->create([
                'day_of_week' => $timeSlot['day_of_week'],
                'local_start_time' => $timeSlot['start_time'],
                'local_end_time' => $timeSlot['end_time'],
                'time_zone' => $userTimeZone,
                'is_recurring' => true,
            ]);
        }

        return redirect()->route('my-adverts');
    }

    public function search(Request $request, AdvertSearchService $searchService): Response
    {
        $filters = $request->validate([
            'tutor_name' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:min_price',
            'min_rating' => 'nullable|integer|min:1|max:5',
            'availability' => 'nullable|array',
            'availability.*.day_of_week' => 'required_with:availability|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.start_time' => 'nullable|date_format:H:i',
            'availability.*.end_time' => 'nullable|date_format:H:i|after:availability.*.start_time',
            'sort_by' => 'nullable|string|in:price_per_hour,rating',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $maxPrice = ceil(Advert::max('price_per_hour') / 100) * 100;

        return Inertia::render('Listings/SearchAdverts', [
            'adverts' => $searchService->search($filters),
            'filters' => $filters,
            'subjects' => Subject::all(['id', 'name']),
            'maxPrice' => $maxPrice,
        ]);
    }

    public function toggleActive(Advert $advert)
    {
        try {
            if ($advert->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $advert->is_active = request('is_active');
            $advert->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error toggling advert status: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update advert status'], 500);
        }
    }
}
