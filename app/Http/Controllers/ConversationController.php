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

        return  $conversations;
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
        $userIds = json_encode($request->user_ids);
        $conversation = Conversation::create([
            'user_ids' => $userIds
        ]);

        return redirect()->route('conversations.show', $conversation->id);
    }



    public function getOne(Request $request)
    {
        $conversation = Conversation::firstOrCreate([
            'user_ids' => json_encode([$request->user_id, $request->recipient_id])
        ]);
        return response()->json(['conversation_id' => $conversation->id]);
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
