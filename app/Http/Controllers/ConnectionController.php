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
        Log::info('Conn request from ' . Auth::user()->id . ' to ' . $userId);

        return back();
    }

    public function accept($userId)
    {
        Log::info('Test log entry');

        try {
            Log::channel('daily')->info('Starting connection accept', [
                'userId' => $userId,
                'authId' => Auth::id()
            ]);

            $connection = User::find($userId)->connections()
                ->where('connected_user_id', Auth::user()->id)
                ->first();

            if (!$connection) {
                Log::channel('daily')->error('Connection not found', [
                    'userId' => $userId,
                    'authId' => Auth::id()
                ]);
                return back()->with('error', 'Connection not found');
            }

            // Debug before updates
            Log::channel('daily')->info('Before connection update', [
                'userId' => $userId,
                'authId' => Auth::id(),
                'connection' => $connection->toArray()
            ]);

            $connection->update([
                'is_accepted' => true
            ]);

            $connection->save();
            return back()->with('success', 'Connection accepted');
            // Rest of your code...
        } catch (\Exception $e) {
            Log::channel('daily')->error('Error in accept connection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
