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
            'paginatedListings' => Listing::with('skills', 'categories')->paginate(2),
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

        return redirect()
            ->route('listings.show', $listing->id)
            ->with('success', 'Listing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        return Inertia::render('Listings/Show', [
            'listing' => $listing,
            'isOwner' => $listing->user_id === Auth::id(),
            'skills' => $listing->skills,
            'categories' => $listing->categories,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        $listing->load(['skills', 'categories']);

        return Inertia::render('Listings/Edit', [
            'listing' => $listing,
            'availableSkills' => Skill::all(),
            'availableCategories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ListingRequest $request, Listing $listing)
    {
        $listing->update($request->all());
        $listing->skills()->sync($request->skills);
        $listing->categories()->sync($request->categories);

        return redirect()
            ->route('listings.show', $listing->id)
            ->with('success', 'Listing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return redirect()
                ->route('listings.show', $listing->id)
                ->with('error', 'You are not authorized to delete this listing!');
        }

        $listing->skills()->detach();
        $listing->categories()->detach();

        $listing->delete();

        return redirect()
            ->route('listings.index')
            ->with('success', 'Listing deleted successfully!');
    }
}
