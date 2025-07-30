<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection that will be used for analytics queries.
    | This should point to your DuckDB or ClickHouse instance.
    |
    */

    'connection' => env('ANALYTICS_DB_CONNECTION', 'duckdb'),

    /*
    |--------------------------------------------------------------------------
    | Query Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum execution time for analytics queries in seconds.
    |
    */

    'query_timeout' => env('ANALYTICS_QUERY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Result Cache
    |--------------------------------------------------------------------------
    |
    | Cache configuration for query results.
    |
    */

    'cache' => [
        'enabled' => env('ANALYTICS_CACHE_ENABLED', true),
        'ttl' => env('ANALYTICS_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'analytics_query_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Limits
    |--------------------------------------------------------------------------
    |
    | Default limits for query results.
    |
    */

    'limits' => [
        'max_rows' => env('ANALYTICS_MAX_ROWS', 10000),
        'default_limit' => env('ANALYTICS_DEFAULT_LIMIT', 1000),
    ],
];
