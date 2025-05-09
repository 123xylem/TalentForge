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

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// GET|  listings  ........ listings.index › ListingController@index
// POST      listings  ........ listings.store › ListingController@store
// GET|  listings/create ....... listings.create › ListingController@create
// GET|  listings/{listing} ........ listings.show › ListingController@show
// PUT|PATCH listings/{listing} .... listings.update › ListingController@update
// DELETE    listings/{listing} .. listings.destroy › ListingController@destroy
// GET|  listings/{listing}/edit ... listings.edit › ListingController@edit
Route::resource('listings', ListingController::class);
Route::resource('categories', CategoryController::class);
Route::resource('skills', SkillController::class);
Route::middleware(['auth', 'verified'])
    ->prefix('listing-applications')
    ->name('listing-applications.')
    ->controller(ListingApplicationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('my-applications');
        Route::post('/', 'store')->name('store');
        Route::get('/{listingApplication}', 'show')->name('show');
        Route::patch('/{listingApplication}', 'update')->name('update');
    });

// Dashboard auth middleware and then let DashboardController handle the routes
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->controller(DashboardController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::post('/connections/{user}', [ConnectionController::class, 'store']);
Route::post('/connections/{user}/accept', [ConnectionController::class, 'accept']);
Route::post('/connections/{user}/decline', [ConnectionController::class, 'decline']);

Route::get('/conversations', [ConversationController::class, 'getOrCreateOneConversation'])
    ->name('conversations.getOrCreateOneConversation');


Route::post('/messages', [MessageController::class, 'store'])
    ->name('messages.store');
