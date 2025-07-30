#!/usr/bin/env php
<?php

/**
 * Test schema analyzer
 * Run: php test-schema.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrap the application
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\DataSource;
use App\Services\AI\SchemaAnalyzer;

echo "=== Schema Test ===\n\n";

// Get first user and data source
$user = User::first();
$dataSource = $user->dataSources()->where('status', 'active')->first();

if (!$dataSource) {
    echo "No active data source found.\n";
    exit(1);
}

echo "Using data source: {$dataSource->name}\n\n";

try {
    $analyzer = app(SchemaAnalyzer::class);
    $schema = $analyzer->getSchemaContext($dataSource);
    
    if (empty($schema)) {
        echo "No schema found. This might mean:\n";
        echo "1. Database connection failed\n";
        echo "2. No tables in the database\n";
        echo "3. Permission issues\n";
    } else {
        echo "Schema found! Tables:\n";
        foreach ($schema as $table => $columns) {
            echo "\nTable: $table\n";
            echo "Columns:\n";
            foreach ($columns as $col) {
                echo "  - {$col['name']} ({$col['type']})\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}