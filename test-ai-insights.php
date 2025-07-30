#!/usr/bin/env php
<?php

/**
 * Test script for AI Insights functionality
 * Run: php test-ai-insights.php
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
use App\Services\AI\AIQueryService;
use App\Services\AI\SchemaAnalyzer;
use App\Services\AI\VisualizationService;
use App\Services\AI\InsightsService;

echo "=== AI Insights Test Script ===\n\n";

// Get first user and data source for testing
$user = User::first();
if (!$user) {
    echo "❌ No users found. Please create a user first.\n";
    exit(1);
}
echo "✅ Using user: {$user->email}\n";

$dataSource = $user->dataSources()->where('status', 'active')->first();
if (!$dataSource) {
    echo "❌ No active data sources found for user. Please create one first.\n";
    exit(1);
}
echo "✅ Using data source: {$dataSource->name} ({$dataSource->type})\n\n";

// Test 1: Schema Analysis
echo "Test 1: Schema Analysis\n";
echo "------------------------\n";
try {
    $schemaAnalyzer = app(SchemaAnalyzer::class);
    $schema = $schemaAnalyzer->getSchemaContext($dataSource);
    
    if (empty($schema)) {
        echo "❌ No schema found\n";
    } else {
        echo "✅ Found " . count($schema) . " tables:\n";
        foreach ($schema as $table => $columns) {
            echo "   - $table (" . count($columns) . " columns)\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Schema analysis failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Natural Language Query
echo "Test 2: Natural Language Query Translation\n";
echo "------------------------------------------\n";
$testQuestions = [
    "Show me all records from the first table",
    "Count total rows in the database",
    "What are the most recent entries?"
];

try {
    $aiQueryService = app(AIQueryService::class);
    
    foreach ($testQuestions as $question) {
        echo "Q: $question\n";
        $result = $aiQueryService->translateNaturalLanguageToSQL($question, $dataSource);
        
        if ($result['success']) {
            echo "✅ SQL: " . $result['sql'] . "\n";
        } else {
            echo "❌ Failed: " . $result['error'] . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "❌ Query translation failed: " . $e->getMessage() . "\n";
}

// Test 3: Visualization Recommendation
echo "Test 3: Visualization Recommendation\n";
echo "------------------------------------\n";
$sampleData = [
    'data' => [
        ['month' => 'Jan', 'sales' => 1000],
        ['month' => 'Feb', 'sales' => 1200],
        ['month' => 'Mar', 'sales' => 1100],
    ],
    'columns' => [
        ['name' => 'month', 'type' => 'string'],
        ['name' => 'sales', 'type' => 'integer'],
    ]
];

try {
    $vizService = app(VisualizationService::class);
    $recommendation = $vizService->recommendVisualization($sampleData);
    
    if ($recommendation['success']) {
        echo "✅ Recommended: " . $recommendation['recommendation']['type'] . "\n";
        echo "   Reason: " . $recommendation['recommendation']['reason'] . "\n";
    } else {
        echo "❌ Visualization recommendation failed\n";
    }
} catch (Exception $e) {
    echo "❌ Visualization service error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Proactive Insights
echo "Test 4: Proactive Insights Generation\n";
echo "-------------------------------------\n";
try {
    $insightsService = app(InsightsService::class);
    $insights = $insightsService->generateProactiveInsights($dataSource);
    
    if ($insights['success'] && !empty($insights['insights'])) {
        echo "✅ Generated " . count($insights['insights']) . " insights:\n";
        foreach ($insights['insights'] as $insight) {
            echo "   - {$insight['title']}: {$insight['description']}\n";
        }
    } else {
        echo "⚠️  No insights generated (this might be normal if there's limited data)\n";
    }
} catch (Exception $e) {
    echo "❌ Insights generation failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nTo test in the browser:\n";
echo "1. Make sure ANTHROPIC_API_KEY is set in .env\n";
echo "2. Run: php artisan serve\n";
echo "3. Visit: http://localhost:8000/insights\n";
echo "4. Select data source: {$dataSource->name}\n";
echo "5. Try asking questions about your data!\n";