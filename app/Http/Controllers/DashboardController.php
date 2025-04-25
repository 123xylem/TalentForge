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

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard view based on user type.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $props = [];
        //TODO handle listings vs applications and how they are rendered
        if ($user->type === 'employer') {
            $props['listings'] = Listing::where('user_id', $user->id)
                ->with(['skills', 'categories'])
                ->latest()
                ->paginate(10);
        } else {
            $props['listings'] = ListingApplication::where('user_id', $user->id)
                ->with(['listing:id,title,description,salary'])
                ->latest()
                ->paginate(10);
        }
        return Inertia::render('Dashboard', $props);
    }
}
