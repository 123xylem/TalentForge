<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;
use App\Models\ListingApplication;
use App\Models\Category;
use App\Models\Skill;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;
use App\Filters\ListingApplicationFilter;
use App\Filters\ListingFilter;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard view based on user type.
     */
    public function index(Request $request, ListingApplicationFilter $filters): Response
    {
        $listingFilters = new ListingFilter($request);
        $employer = Auth::user()->type === 'employer';
        $page = request()->input('page', 1);
        $user = Auth::user()->id;
        $filterParamCount = count($request->all()) > 0;
        $filterParams = $request->all();

        if ($employer) {
            if ($filterParamCount) {
                $listings = Listing::filter($listingFilters)->where('user_id', $user)->with('skills', 'categories')->withCount('applicants')->latest()->paginate(6);
            } else {
                $cacheKey = 'listing_page_' . $page;
                $listings = Cache::remember($cacheKey, 60 * 120, function () use ($user) {
                    return Listing::where('user_id', $user)->with('skills', 'categories')->withCount('applicants')->paginate(6);
                });
            }
        } else {
            if ($filterParamCount) {
                $listings = ListingApplication::whereHas('listing', function ($query) use ($filterParams) {
                    $query->when(
                        $filterParams['textSearch'] ?? null,
                        fn($q) => $q->where(function ($q2) use ($filterParams) {
                            $q2->where('title', 'like', "%{$filterParams['textSearch']}%")
                                ->orWhere('description', 'like', "%{$filterParams['textSearch']}%");
                        })
                    )
                        ->when(
                            $filterParams['salary'] ?? null,
                            fn($q) =>
                            $q->where('salary', $filterParams['salary'])
                        )
                        ->when(
                            $filterParams['locationSearch'] ?? null,
                            fn($q) =>
                            $q->where('location', 'like', "%{$filterParams['locationSearch']}%")
                        )
                        ->when(
                            $filterParams['category'] ?? null,
                            fn($q) =>
                            $q->whereHas('categories', function ($query) use ($filterParams) {
                                $query->where('category_id', $filterParams['category']);
                            })
                        )
                        ->when(
                            $filterParams['skills'] ?? null,
                            fn($q) =>
                            $q->whereHas('skills', function ($query) use ($filterParams) {
                                $query->whereIn('skill_id', $filterParams['skills']);
                            })
                        )
                        ->when(
                            $filterParams['applicationStatus'] ?? null,
                            fn($q) =>
                            $q->where('listing_applications.status', $filterParams['applicationStatus'])
                        );
                })->with('listing:id,title,description,salary')->latest()->paginate(6);
            } else {
                $cacheKey = 'listing_applications_page_' . $page;
                $listings = Cache::remember($cacheKey, 60 * 120, function () use ($user) {
                    return ListingApplication::where('user_id', $user)
                        ->with('listing:id,title,description,salary')
                        ->latest()
                        ->paginate(6);
                });
            }
        }
        return Inertia::render('Dashboard', [
            'listings' => $listings,
        ]);

        // return Inertia::render('Dashboard', $props);
    }
}
