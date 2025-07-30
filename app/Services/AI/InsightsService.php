<?php

namespace App\Services\AI;

use App\Models\DataSource;
use App\Services\Query\QueryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class InsightsService
{
    protected QueryService $queryService;

    protected SchemaAnalyzer $schemaAnalyzer;

    public function __construct(QueryService $queryService, SchemaAnalyzer $schemaAnalyzer)
    {
        $this->queryService = $queryService;
        $this->schemaAnalyzer = $schemaAnalyzer;
    }

    public function generateProactiveInsights(DataSource $dataSource): array
    {
        $cacheKey = 'proactive_insights:'.$dataSource->id;

        return Cache::remember($cacheKey, 1800, function () use ($dataSource) {
            try {
                $schema = $this->schemaAnalyzer->getSchemaContext($dataSource);
                $insights = [];

                // Analyze key metrics
                $metricInsights = $this->analyzeKeyMetrics($dataSource, $schema);
                $insights = array_merge($insights, $metricInsights);

                // Detect anomalies
                $anomalies = $this->detectAnomalies($dataSource, $schema);
                $insights = array_merge($insights, $anomalies);

                // Find trends
                $trends = $this->analyzeTrends($dataSource, $schema);
                $insights = array_merge($insights, $trends);

                return [
                    'success' => true,
                    'insights' => array_slice($insights, 0, 5), // Limit to top 5 insights
                    'generated_at' => now()->toIso8601String(),
                ];
            } catch (\Exception $e) {
                Log::error('Proactive insights generation failed', [
                    'data_source_id' => $dataSource->id,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'insights' => [],
                    'error' => 'Failed to generate insights',
                ];
            }
        });
    }

    public function explainQueryResult(array $queryResult, string $originalQuestion): string
    {
        try {
            $summary = $this->summarizeQueryResult($queryResult);

            $prompt = <<<PROMPT
Based on this question: "$originalQuestion"
And these query results:
$summary

Provide a brief, conversational explanation of what the data shows.
Focus on key findings and insights relevant to the question.
Keep it under 3 sentences.
PROMPT;

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withPrompt($prompt)
                ->withMaxTokens(150)
                ->asText();

            return $response->text;
        } catch (\Exception $e) {
            Log::error('Failed to explain query result', [
                'error' => $e->getMessage(),
            ]);

            return 'The query returned '.count($queryResult['data'] ?? []).' results.';
        }
    }

    protected function analyzeKeyMetrics(DataSource $dataSource, array $schema): array
    {
        $insights = [];

        // Find numeric columns that might be key metrics
        foreach ($schema as $table => $columns) {
            $numericColumns = array_filter($columns, function ($col) {
                return in_array($col['type'], ['int', 'integer', 'float', 'double', 'decimal', 'numeric']);
            });

            foreach ($numericColumns as $column) {
                $insight = $this->analyzeMetricColumn($dataSource, $table, $column['name']);
                if ($insight) {
                    $insights[] = $insight;
                }
            }
        }

        return $insights;
    }

    protected function analyzeMetricColumn(DataSource $dataSource, string $table, string $column): ?array
    {
        try {
            // Get basic statistics
            $sql = "SELECT 
                COUNT($column) as count,
                AVG($column) as average,
                MIN($column) as minimum,
                MAX($column) as maximum
                FROM $table
                WHERE $column IS NOT NULL";

            $result = $this->queryService->executeQuery($dataSource, $sql, 1);

            if (! $result['success'] || empty($result['data'])) {
                return null;
            }

            $stats = $result['data'][0];

            // Generate insight based on statistics
            $columnDisplay = ucwords(str_replace('_', ' ', $column));

            return [
                'type' => 'metric_summary',
                'title' => "$columnDisplay Overview",
                'description' => sprintf(
                    'The %s ranges from %s to %s with an average of %s',
                    strtolower($columnDisplay),
                    number_format($stats['minimum'], 2),
                    number_format($stats['maximum'], 2),
                    number_format($stats['average'], 2)
                ),
                'data' => $stats,
                'query' => "What is the average $column?",
                'priority' => 3,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function detectAnomalies(DataSource $dataSource, array $schema): array
    {
        $insights = [];

        // Look for potential time-based anomalies
        foreach ($schema as $table => $columns) {
            $timeColumn = $this->findTimeColumn($columns);
            $metricColumns = $this->findMetricColumns($columns);

            if ($timeColumn && ! empty($metricColumns)) {
                foreach ($metricColumns as $metric) {
                    $anomaly = $this->detectTimeBasedAnomaly($dataSource, $table, $timeColumn, $metric);
                    if ($anomaly) {
                        $insights[] = $anomaly;
                    }
                }
            }
        }

        return $insights;
    }

    protected function detectTimeBasedAnomaly(DataSource $dataSource, string $table, string $timeColumn, string $metricColumn): ?array
    {
        try {
            // Compare recent period to historical average
            $sql = "WITH recent AS (
                SELECT AVG($metricColumn) as recent_avg
                FROM $table
                WHERE $timeColumn >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                    AND $metricColumn IS NOT NULL
            ),
            historical AS (
                SELECT AVG($metricColumn) as hist_avg, STDDEV($metricColumn) as hist_stddev
                FROM $table
                WHERE $timeColumn >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                    AND $timeColumn < DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                    AND $metricColumn IS NOT NULL
            )
            SELECT 
                recent.recent_avg,
                historical.hist_avg,
                historical.hist_stddev,
                ABS(recent.recent_avg - historical.hist_avg) / NULLIF(historical.hist_stddev, 0) as zscore
            FROM recent, historical";

            $result = $this->queryService->executeQuery($dataSource, $sql, 1);

            if (! $result['success'] || empty($result['data'])) {
                return null;
            }

            $data = $result['data'][0];
            $zscore = $data['zscore'] ?? 0;

            // Significant anomaly if z-score > 2
            if ($zscore > 2) {
                $change = (($data['recent_avg'] - $data['hist_avg']) / $data['hist_avg']) * 100;
                $direction = $change > 0 ? 'increased' : 'decreased';

                return [
                    'type' => 'anomaly',
                    'title' => 'Unusual '.ucwords(str_replace('_', ' ', $metricColumn)),
                    'description' => sprintf(
                        '%s has %s by %.1f%% in the last 7 days compared to the previous 30-day average',
                        ucwords(str_replace('_', ' ', $metricColumn)),
                        $direction,
                        abs($change)
                    ),
                    'data' => $data,
                    'query' => "Show me recent changes in $metricColumn",
                    'priority' => 1,
                ];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function analyzeTrends(DataSource $dataSource, array $schema): array
    {
        $insights = [];

        foreach ($schema as $table => $columns) {
            $timeColumn = $this->findTimeColumn($columns);
            $metricColumns = $this->findMetricColumns($columns);

            if ($timeColumn && ! empty($metricColumns)) {
                foreach ($metricColumns as $metric) {
                    $trend = $this->analyzeTrend($dataSource, $table, $timeColumn, $metric);
                    if ($trend) {
                        $insights[] = $trend;
                    }
                }
            }
        }

        return $insights;
    }

    protected function analyzeTrend(DataSource $dataSource, string $table, string $timeColumn, string $metricColumn): ?array
    {
        try {
            // Simple trend analysis - compare month over month
            $sql = "WITH monthly AS (
                SELECT 
                    DATE_FORMAT($timeColumn, '%Y-%m') as month,
                    SUM($metricColumn) as total
                FROM $table
                WHERE $timeColumn >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)
                    AND $metricColumn IS NOT NULL
                GROUP BY DATE_FORMAT($timeColumn, '%Y-%m')
                ORDER BY month
            )
            SELECT 
                month,
                total,
                LAG(total) OVER (ORDER BY month) as prev_total
            FROM monthly";

            $result = $this->queryService->executeQuery($dataSource, $sql, 10);

            if (! $result['success'] || count($result['data']) < 2) {
                return null;
            }

            // Calculate growth rate
            $growthRates = [];
            foreach ($result['data'] as $row) {
                if ($row['prev_total'] && $row['prev_total'] > 0) {
                    $growthRates[] = (($row['total'] - $row['prev_total']) / $row['prev_total']) * 100;
                }
            }

            if (empty($growthRates)) {
                return null;
            }

            $avgGrowth = array_sum($growthRates) / count($growthRates);
            $direction = $avgGrowth > 0 ? 'growing' : 'declining';

            return [
                'type' => 'trend',
                'title' => ucwords(str_replace('_', ' ', $metricColumn)).' Trend',
                'description' => sprintf(
                    '%s is %s at an average rate of %.1f%% per month',
                    ucwords(str_replace('_', ' ', $metricColumn)),
                    $direction,
                    abs($avgGrowth)
                ),
                'data' => [
                    'average_growth' => $avgGrowth,
                    'months_analyzed' => count($result['data']),
                ],
                'query' => "Show me the trend for $metricColumn over time",
                'priority' => 2,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function findTimeColumn(array $columns): ?string
    {
        $timePatterns = ['created_at', 'updated_at', 'date', 'timestamp', 'datetime'];

        foreach ($columns as $column) {
            foreach ($timePatterns as $pattern) {
                if (stripos($column['name'], $pattern) !== false) {
                    return $column['name'];
                }
            }

            if (in_array($column['type'], ['date', 'datetime', 'timestamp'])) {
                return $column['name'];
            }
        }

        return null;
    }

    protected function findMetricColumns(array $columns): array
    {
        $metrics = [];
        $excludePatterns = ['id', 'key', 'uuid', 'hash'];

        foreach ($columns as $column) {
            // Skip ID-like columns
            $skip = false;
            foreach ($excludePatterns as $pattern) {
                if (stripos($column['name'], $pattern) !== false) {
                    $skip = true;
                    break;
                }
            }

            if (! $skip && in_array($column['type'], ['int', 'integer', 'float', 'double', 'decimal', 'numeric'])) {
                $metrics[] = $column['name'];
            }
        }

        return array_slice($metrics, 0, 3); // Limit to avoid too many queries
    }

    protected function summarizeQueryResult(array $queryResult): string
    {
        $rowCount = count($queryResult['data'] ?? []);

        if ($rowCount === 0) {
            return 'No data found';
        }

        $summary = "Found $rowCount rows.\n";

        // Add column summary
        if (! empty($queryResult['columns'])) {
            $columnNames = array_column($queryResult['columns'], 'name');
            $summary .= 'Columns: '.implode(', ', $columnNames)."\n";
        }

        // Add sample data
        if ($rowCount > 0) {
            $firstRow = $queryResult['data'][0];
            $summary .= 'Sample row: '.json_encode($firstRow);
        }

        return $summary;
    }
}
