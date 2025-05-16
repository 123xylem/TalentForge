<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewMessage;
use App\Notifications\MessageRecieved;
use App\Models\Conversation;
use App\Models\User;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $messages = Message::where('conversation_id', $request->conversation_id)->latest()
            ->limit(30)
            ->get()
            ->reverse()
            ->values();

        return $messages;
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
        $message = Message::create([
            'content' => $request->content,
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::user()->id,
        ]);

        $this->broadCastAndNotifyRecipient($request->content, $request->conversation_id, Auth::user()->id, $message->created_at);
        return back();
    }

    private function broadCastAndNotifyRecipient($content, $conversationId, $userId, $createdAt)
    {

        $conversationUsers = Conversation::find($conversationId)->user_ids;
        $recipient = null;
        $sender = null;
        foreach ($conversationUsers as $id) {
            if ($id !== $userId) {
                $recipient = User::find($id);
            } else {
                $sender = User::find($id);
            }
        }
        broadcast(new NewMessage($content, $conversationId, $userId, $createdAt));
        $recipient->notify(new MessageRecieved($content, $conversationId, $sender->id, $createdAt));
        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
