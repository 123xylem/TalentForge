<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;
use App\Models\Application;
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

        if ($user->type === 'job_hunter') {
            $props['listings'] = Listing::where('user_id', $user->id)
                ->with(['skills', 'categories'])
                ->latest()
                ->paginate(10);
        } else {
            $props['applications'] = Application::where('user_id', $user->id)
                ->with('listing')
                ->latest()
                ->paginate(10);
        }
        return Inertia::render('Dashboard', $props);
    }
}
