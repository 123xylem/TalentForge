<?php

namespace App\Http\Controllers;


use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Skill;
use App\Http\Requests\ListingRequest;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Listings/Index', [
            'listings' => Listing::all(),
            'flash' => [
                'errors' => session('errors', new \Illuminate\Support\ViewErrorBag),
                'success' => session('success', null),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Listings/Create', [
            'availableSkills' => Skill::all(),
            'availableCategories' => Category::all(),
            'flash' => [
                'errors' => session('errors', new \Illuminate\Support\ViewErrorBag),
                'success' => session('success', null),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ListingRequest $request)
    {
        $user_id = Auth::id();
        $listing = Listing::create([...$request->all(), 'user_id' => $user_id]);
        $listing->skills()->attach($request->skills);
        $listing->categories()->attach($request->categories);
        $listing->save();
        return Inertia::render('Listings/Index', [
            'listings' => Listing::all(),
            'flash' => [
                'errors' => session('errors', new \Illuminate\Support\ViewErrorBag),
                'success' => session('success', null),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        //
    }
}
