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

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::resource('listings', ListingController::class)->middleware(['auth', 'verified']);
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
    Route::post('/connections/{user}', [ConnectionController::class, 'store']);
    Route::post('/connections/{user}/accept', function ($user) {
        \Log::error([
            'auth' => Auth::check(),
            'user' => Auth::user(),
            'verified' => Auth::user()?->hasVerifiedEmail(),
            'request' => request()->all(),
            'headers' => request()->headers->all()
        ]);


        return [ConnectionController::class, 'accept']($user);
    });
    Route::post('/connections/{user}/decline', [ConnectionController::class, 'decline']);

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

Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-CSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('RANDOM_VALUE', 'RANDOM_VALUE');
})->where('any', '.*');


Route::get('/env-test', function () {
    return [
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
        'direct_env' => config('app.url'),
        'base_url' => url('/'),
        'asset_url' => asset('/'),
    ];
});
