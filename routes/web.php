<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataSourceController;
use App\Http\Controllers\QueryController;
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
    Route::post('dashboards/{dashboard}/widgets', [DashboardController::class, 'addWidget'])->name('dashboards.widgets.store');
    Route::put('dashboards/{dashboard}/widgets/{widget}', [DashboardController::class, 'updateWidget'])->name('dashboards.widgets.update');
    Route::delete('dashboards/{dashboard}/widgets/{widget}', [DashboardController::class, 'deleteWidget'])->name('dashboards.widgets.destroy');
    Route::put('dashboards/{dashboard}/widgets/reorder', [DashboardController::class, 'reorderWidgets'])->name('dashboards.widgets.reorder');
    
    Route::resource('data-sources', DataSourceController::class);
    Route::post('data-sources/{data_source}/test', [DataSourceController::class, 'test'])->name('data-sources.test');
    Route::get('data-source-config-fields', [DataSourceController::class, 'getConfigFields'])->name('data-sources.config-fields');
    
    // Query routes
    Route::get('queries', [QueryController::class, 'index'])->name('queries.index');
    Route::get('data-sources/{data_source}/queries', [QueryController::class, 'index'])->name('data-sources.queries.index');
    Route::get('data-sources/{data_source}/query', [QueryController::class, 'editor'])->name('query.editor');
    Route::get('data-sources/{data_source}/query/{query}', [QueryController::class, 'editor'])->name('query.editor.saved');
    Route::post('data-sources/{data_source}/query/execute', [QueryController::class, 'execute'])->name('query.execute');
    Route::post('data-sources/{data_source}/query/save', [QueryController::class, 'save'])->name('query.save');
    Route::delete('queries/{query}', [QueryController::class, 'destroy'])->name('query.destroy');

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
