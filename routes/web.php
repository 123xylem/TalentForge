<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;

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


// Dashboard auth middleware and then let DashboardController handle the routes
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->controller(DashboardController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
