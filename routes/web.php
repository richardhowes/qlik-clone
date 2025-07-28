<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataSourceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // BI Tool Routes
    Route::resource('dashboards', DashboardController::class);
    Route::post('dashboards/{dashboard}/favorite', [DashboardController::class, 'toggleFavorite'])->name('dashboards.favorite');
    
    Route::resource('data-sources', DataSourceController::class);
    Route::post('data-sources/{data_source}/test', [DataSourceController::class, 'test'])->name('data-sources.test');
    Route::get('data-source-config-fields', [DataSourceController::class, 'getConfigFields'])->name('data-sources.config-fields');

    Route::get('insights', function () {
        return Inertia::render('Insights');
    })->name('insights');

    Route::get('favorites', function () {
        return Inertia::render('Favorites');
    })->name('favorites');

    Route::get('collections', function () {
        return Inertia::render('Collections');
    })->name('collections');

    Route::get('browse', function () {
        return Inertia::render('Browse');
    })->name('browse');

    Route::get('learn', function () {
        return Inertia::render('Learn');
    })->name('learn');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
