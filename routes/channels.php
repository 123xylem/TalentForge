<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
  \Log::info('Chat Subscription', [
    'user' => $user->id,
    'conversationId' => $conversationId,
    'exists' => Conversation::where('id', $conversationId)
      ->whereJsonContains('user_ids', $user->id)
      ->exists()
  ]);
  return Conversation::where('id', $conversationId)
    ->whereJsonContains('user_ids', $user->id)
    ->exists();
});
