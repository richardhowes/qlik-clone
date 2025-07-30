<?php

namespace App\Providers;

use App\Services\AI\AIQueryService;
use App\Services\AI\InsightsService;
use App\Services\AI\SchemaAnalyzer;
use App\Services\AI\VisualizationService;
use App\Services\DataSource\ConnectionManager;
use App\Services\Query\QueryService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AI services as singletons for better performance
        $this->app->singleton(SchemaAnalyzer::class, function ($app) {
            return new SchemaAnalyzer($app->make(ConnectionManager::class));
        });

        $this->app->singleton(AIQueryService::class, function ($app) {
            return new AIQueryService(
                $app->make(QueryService::class),
                $app->make(SchemaAnalyzer::class)
            );
        });

        $this->app->singleton(InsightsService::class, function ($app) {
            return new InsightsService(
                $app->make(QueryService::class),
                $app->make(SchemaAnalyzer::class)
            );
        });

        $this->app->singleton(VisualizationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
