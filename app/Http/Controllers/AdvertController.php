<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdvertController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Adverts/MyAdverts', [
            'adverts' => auth()->user()->adverts()->with('subject')->get()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Adverts/CreateAdvert', [
            'subjects' => Subject::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $request->user()->adverts()->create($validatedData);

        return redirect()->route('adverts.index')
            ->with('success', 'Advert created successfully.');
    }
}
