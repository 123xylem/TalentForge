<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('private.conversation.{conversationId}', function (User $user, int $conversationId) {
  return $user->isInConversation($conversationId);
});
