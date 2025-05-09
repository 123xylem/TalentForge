<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {

  return Auth::user()->id === $user->id;
});
