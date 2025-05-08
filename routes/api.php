<?php

use App\Http\Controllers\ConversationController;
use Illuminate\Support\Facades\Route;

Route::get('/conversations', [ConversationController::class, 'getOne'])
  ->name('conversations.getOne');
