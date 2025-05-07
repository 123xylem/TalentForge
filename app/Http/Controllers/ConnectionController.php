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

        // Optional: Send notification to target user
        $targetUser = User::find($userId);
        $targetUser->notify(new ConnectionRequest(Auth::user()));

        return back();
    }

    public function accept($userId)
    {
        // Accept connection
        Auth::user()->connections()
            ->updateExistingPivot($userId, ['is_accepted' => true]);

        return back();
    }
}
