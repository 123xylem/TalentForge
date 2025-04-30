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
        $employer = Auth::user()->type === 'employer';
        $page = request()->input('page', 1);
        $user = Auth::user()->id;
        // $props = [];
        $filterParams = count($request->all()) > 0;

        if ($employer) {
            if ($filterParams) {
                $listings = Listing::filter($filters)->paginate(6);
            } else {
                $cacheKey = 'listing_applications_page_' . $page;
                $listings = Cache::remember($cacheKey, 60 * 120, function () use ($user) {
                    return Listing::where('user_id', $user)->with('skills', 'categories')->paginate(6);
                });
            }

            // $props['listings'] = Listing::where('user_id', $user->id)
            //     ->with(['skills', 'categories'])
            //     ->latest()
            //     ->paginate(10);
        } else {
            if ($filterParams) {
                $listings = ListingApplication::filter($filters)->paginate(6);
            } else {
                $cacheKey = 'listing_applications_page_' . $page;
                $listings = Cache::remember($cacheKey, 60 * 120, function () use ($user) {
                    return ListingApplication::where('user_id', $user)->with('listing:id,title,description,salary')->latest()->paginate(6);
                });
            }
            //     return Inertia::render('Dashboard', [
            //         'listings' => $listings,
            //     ]);



            //     $props['listings'] = ListingApplication::where('user_id', $user->id)
            //         ->with(['listing:id,title,description,salary'])
            //         ->latest()
            //         ->paginate(10);
            // }
        }
        return Inertia::render('Dashboard', [
            'listings' => $listings,
        ]);

        // return Inertia::render('Dashboard', $props);
    }
}
