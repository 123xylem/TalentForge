<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
  return $user->isInConversation($conversationId);
});
