<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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



Route::middleware(['auth'])
    ->prefix('dashboard')
    ->name('listings.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Auth::user()->type === 'employer'
                ? redirect()->route('dashboard.listings')
                : redirect()->route('dashboard.applications');
        })->name('dashboard');

        Route::get('/dashboard/listings', [DashboardController::class, 'listings'])
            ->name('dashboard.listings')
            ->middleware('can:view-employer-dashboard');

        Route::get('/dashboard/applications', [DashboardController::class, 'applications'])
            ->name('dashboard.applications')
            ->middleware('can:view-jobseeker-dashboard');
    });
