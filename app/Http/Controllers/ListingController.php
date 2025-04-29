<?php

namespace App\Http\Controllers;


use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Skill;
use App\Http\Requests\ListingRequest;
use App\Models\ListingApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Filters\ListingFilter;


class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListingFilter $filters)
    {
        $filterParams = count($request->all()) > 0;
        if ($filterParams) {
            $listings = Listing::filter($filters)->paginate(6);
        } else {
            $page = request()->input('page', 1);
            $cacheKey = 'listings_page_' . $page;
            $listings = Cache::remember($cacheKey, 60 * 120, function () {
                return Listing::with('skills', 'categories')->paginate(6);
            });
        }
        return Inertia::render('Listings/Index', [
            'paginatedListingData' => $listings,
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
        Cache::forget('listings');
        Cache::put('listings', Listing::with('skills', 'categories'));

        return redirect()
            ->route('listings.show', $listing->id)
            ->with('flash.success', 'Listing created successfully!');
        // ->with('userApplicationStatus', 'applied');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $isOwner = $listing->user_id === Auth::id();
        if (!$isOwner) {
            $userApplicationStatus = $listing->applications->where('user_id', Auth::id())->first()->status ?? null;
        }
        if ($isOwner) {
            $listingApplications = $listing->applications;
            $listingApplicationsData = [];
            foreach ($listingApplications as $listingApplication) {
                $applicant = $listingApplication->applicant;
                $name = $applicant->name;
                $email = $applicant->email;
                $phone = $applicant->phone;
                $address = $applicant->location;
                $listingApplicationsData[] = [
                    'id' => $listingApplication->id,
                    'cv' => Storage::url($listingApplication->cv),
                    'cover_letter' => $listingApplication->cover_letter,
                    'status' => $listingApplication->status,
                    'created_at' => $listingApplication->created_at,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                ];
            }
        }
        $listingData = Cache::remember('listing' . $listing->id, 60 * 120, function () use ($listing) {
            return $listing;
        });
        return Inertia::render('Listings/Show', [
            'listing' => $listingData,
            'userApplicationStatus' => $userApplicationStatus ?? null,
            'isOwner' => $isOwner,
            'listingApplications' => $listingApplicationsData ?? [],
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
        Cache::forget('listing' . $listing->id);
        Cache::put('listing' . $listing->id, $listing);
        return redirect()
            ->route('listings.show', $listing->id)
            ->with('flash', ['success' => 'Listing updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return redirect()
                ->route('listings.show', $listing->id)
                ->with('flash', ['error' => 'You are not authorized to delete this listing!']);
        }

        $listing->skills()->detach();
        $listing->categories()->detach();

        $listing->delete();
        Cache::forget('listing' . $listing->id);
        Cache::forget('listings');
        Cache::put('listings', Listing::with('skills', 'categories'));
        return redirect()
            ->route('listings.index')
            ->with('flash', ['success' => 'Listing deleted successfully!']);
    }
}
