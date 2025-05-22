<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ConnectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConnectionController extends Controller
{
    public function store(Request $request, $userId)
    {
        // Create connection request - both sides start as pending
        Auth::user()->connections()->attach($userId, [
            'is_accepted' => true
        ]);

        User::find($userId)->connections()->attach(Auth::id(), [
            'is_accepted' => false
        ]);

        // Optional: Send notification to target user
        $targetUser = User::find($userId);
        $targetUser->notify(new ConnectionRequest(Auth::user()));

        return back();
    }

    public function accept($userId)
    {

        try {
            // Update both sides of the connection
            User::find($userId)->connections()
                ->updateExistingPivot(Auth::id(), ['is_accepted' => true]);

            Auth::user()->connections()
                ->updateExistingPivot($userId, ['is_accepted' => true]);

            if (request()->header('X-Inertia')) {
                return back()->with('success', 'Connection accepted');
            }

            return response()->json(['message' => 'Connection accepted']);
        } catch (\Exception $e) {
            Log::error('Error in accept connection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'userId' => $userId,
                'authId' => Auth::id()
            ]);

            return back()->with('error', 'Error accepting connection');
        }
    }

    public function decline($userId)
    {
        User::find($userId)->connections()
            ->updateExistingPivot(Auth::user()->id, ['is_accepted' => false]);

        User::find(Auth::user()->id)->connections()
            ->updateExistingPivot($userId, ['is_accepted' => false]);

        return back()->with('flash', ['success' => 'Connection declined']);
    }
}
