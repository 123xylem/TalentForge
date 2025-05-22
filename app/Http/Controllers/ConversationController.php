<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $conversations = Conversation::where('user_id', Auth::user()->id)->get();
        return $conversations->map(function ($conversation) {
            return $conversation->messages;
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $userIds = json_encode($request->user_ids);
        // $userIds = [$request->user_ids];
        // sort($userIds);
        // $userIds = json_encode($userIds);
        // $conversation = Conversation::create([
        //     'user_ids' => $userIds
        // ]);

        // return redirect()->route('conversations.show', $conversation->id);
    }



    public function getOrCreateOne(Request $request)
    {
        // $userIds = [$request->recipient_id, $request->user_id];
        // sort($userIds);
        // dd($userIds, json_encode($userIds));
        // $conversation = Conversation::firstOrCreate([
        //     'user_ids' => json_encode($userIds)  // Save as JSON string
        // ]);

        $userIds = [$request->recipient_id, $request->user_id];
        sort($userIds);

        // Convert to integers for consistent comparison
        $userIds = array_map('intval', $userIds);
        $names = [];
        foreach ($userIds as $id) {
            $names[$id] = User::find($id)->name;
        }
        // Find existing conversation
        $conversation = Conversation::where(function ($query) use ($userIds) {
            $query->whereJsonContains('user_ids', $userIds[0])
                ->whereJsonContains('user_ids', $userIds[1]);
        })->first();
        \Log::info('Conversation', [
            'conversation' => $conversation,
            'userIds' => $userIds
        ]);
        // Create if not found
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_ids' => $userIds
            ]);
        }

        return response()->json(['conversation_id' => $conversation->id, 'names' => $names]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
}
