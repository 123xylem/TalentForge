<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingApplicationController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::resource('listings', ListingController::class);
Route::resource('categories', CategoryController::class);
Route::resource('skills', SkillController::class);

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Listing Applications
    Route::prefix('listing-applications')
        ->name('listing-applications.')
        ->controller(ListingApplicationController::class)
        ->group(function () {
            Route::get('/', 'index')->name('my-applications');
            Route::post('/', 'store')->name('store');
            Route::get('/{listingApplication}', 'show')->name('show');
            Route::patch('/{listingApplication}', 'update')->name('update');
        });

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Connections
    Route::prefix('connections')
        ->name('connections.')
        ->controller(ConnectionController::class)
        ->group(function () {
            Route::post('/{user}', 'store')->name('store');
            Route::post('/{user}/accept', 'accept')->name('accept');
            Route::post('/{user}/decline', 'decline')->name('decline');
        });

    // Conversations
    Route::get('/conversations', [ConversationController::class, 'getOrCreateOne'])
        ->name('conversations.getOrCreateOne');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])
        ->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])
        ->name('messages.store');

    // Dashboard
    Route::prefix('dashboard')
        ->name('dashboard.')
        ->controller(DashboardController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });
});

Route::get('/env-test', function () {
    return [
        // 'all' => $_ENV,
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
        'direct_env' => config('app.url'),
        'base_url' => url('/'),
        'asset_url' => asset('/'),
    ];
})->middleware(['auth', 'verified'])->name('env-test');
