<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ConnectionRequest;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    public function store(Request $request, $userId)
    {

        // Create connection request
        Auth::user()->connections()->attach($userId, [
            'is_accepted' => false
        ]);

        User::find($userId)->connections()->attach(Auth::id(), [
            'is_accepted' => true
        ]);

        // Optional: Send notification to target user
        $targetUser = User::find($userId);
        $targetUser->notify(new ConnectionRequest(Auth::user()));

        return back();
    }

    public function accept($userId)
    {
        $connection = User::find($userId)->connections()
            ->where('connected_user_id', Auth::user()->id)
            ->first();

        if (!$connection) {
            return response()->json(['error' => 'Connection not found'], 404);
        }

        // Accept connection
        User::find($userId)->connections()
            ->updateExistingPivot(Auth::user()->id, ['is_accepted' => true]);

        return back()->with('flash', ['success' => 'Connection accepted']);
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
