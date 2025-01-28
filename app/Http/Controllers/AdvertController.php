<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Currency;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
        return Inertia::render('Listings/CreateAdvert', [
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

        return redirect()->route('adverts.index');
    }

    public function show(Advert $advert): Response
    {
        return Inertia::render('Listings/ShowAdvert', [
            'advert' => $advert->load(['subject', 'currency', 'availableTimes'])
        ]);
    }
}
