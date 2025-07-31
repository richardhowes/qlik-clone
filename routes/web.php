<?php

use App\Http\Controllers\AiInsightsController;
use App\Http\Controllers\AiTestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataSourceController;
use App\Http\Controllers\DebugController;
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

    // AI Insights Routes
    Route::get('insights', [AiInsightsController::class, 'index'])->name('insights');
    Route::post('insights/ask', [AiInsightsController::class, 'askQuestion'])->name('insights.ask');
    Route::get('insights/proactive', [AiInsightsController::class, 'getProactiveInsights'])->name('insights.proactive');
    Route::post('insights/visualization', [AiInsightsController::class, 'generateVisualization'])->name('insights.visualization');
    Route::post('insights/save', [AiInsightsController::class, 'saveInsight'])->name('insights.save');
    Route::get('insights/debug', [AiInsightsController::class, 'debugDataSource'])->name('insights.debug');

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

    // AI Test Routes
    Route::get('ai/test', [AiTestController::class, 'index'])->name('ai.test');
    Route::post('ai/generate', [AiTestController::class, 'generate'])->name('ai.generate');
    
    // Debug route (remove in production)
    Route::get('debug/schema', [DebugController::class, 'checkSchema'])->name('debug.schema');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
