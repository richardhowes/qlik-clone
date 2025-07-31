<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class VisualizationService
{
    public function recommendVisualization(array $queryResults, string $question = ''): array
    {
        try {
            if (empty($queryResults['data'])) {
                return $this->getDefaultVisualization();
            }

            $dataCharacteristics = $this->analyzeDataCharacteristics($queryResults);
            $recommendation = $this->getVisualizationRecommendation($dataCharacteristics, $question);

            return [
                'success' => true,
                'recommendation' => $recommendation,
                'alternatives' => $this->getAlternativeVisualizations($dataCharacteristics),
            ];
        } catch (\Exception $e) {
            Log::error('Visualization recommendation failed', [
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultVisualization();
        }
    }

    public function generateChartConfig(array $queryResults, string $chartType): array
    {
        $columns = $queryResults['columns'] ?? [];
        $data = $queryResults['data'] ?? [];

        if (empty($columns) || empty($data)) {
            return [];
        }

        $dimensions = $this->identifyDimensions($columns, $data);
        $metrics = $this->identifyMetrics($columns, $data);

        switch ($chartType) {
            case 'bar':
            case 'line':
                return $this->generateAxisBasedConfig($dimensions, $metrics, $chartType);

            case 'pie':
                return $this->generatePieConfig($dimensions, $metrics);

            case 'scatter':
                return $this->generateScatterConfig($dimensions, $metrics);

            case 'table':
                return $this->generateTableConfig($columns);

            default:
                return [];
        }
    }

    public function analyzeDataCharacteristics(array $queryResults): array
    {
        $data = $queryResults['data'];
        $columns = $queryResults['columns'] ?? [];

        $characteristics = [
            'row_count' => count($data),
            'column_count' => count($columns),
            'has_time_series' => false,
            'has_categories' => false,
            'has_numeric_data' => false,
            'numeric_columns' => [],
            'categorical_columns' => [],
            'time_columns' => [],
            'comparison_pattern' => null,
            'grouping_columns' => [],
        ];

        if (empty($data)) {
            return $characteristics;
        }

        // Analyze each column
        foreach ($columns as $column) {
            $columnName = $column['name'];
            $columnType = $column['type'] ?? 'string';

            // Sample values for better type detection
            $sampleValues = array_slice(array_column($data, $columnName), 0, 10);

            if ($this->isTimeColumn($columnName, $columnType, $sampleValues)) {
                $characteristics['has_time_series'] = true;
                $characteristics['time_columns'][] = $columnName;
            } elseif ($this->isNumericColumn($columnType, $sampleValues)) {
                $characteristics['has_numeric_data'] = true;
                $characteristics['numeric_columns'][] = $columnName;
            } else {
                $characteristics['has_categories'] = true;
                $characteristics['categorical_columns'][] = $columnName;
            }
        }

        // Detect comparison patterns
        $characteristics['comparison_pattern'] = $this->detectComparisonPattern($data, $columns, $characteristics);

        return $characteristics;
    }

    protected function getVisualizationRecommendation(array $characteristics, string $question): array
    {
        // Check for comparison patterns first
        if ($characteristics['comparison_pattern']) {
            $pattern = $characteristics['comparison_pattern'];

            if ($pattern['type'] === 'year_over_year') {
                return [
                    'type' => 'line',
                    'reason' => 'Line charts are ideal for comparing trends across different years',
                    'config' => [
                        'xAxis' => $pattern['category_column'],
                        'yAxis' => $pattern['metric_columns'][0] ?? null,
                        'series' => $pattern['grouping_column'],
                        'comparison' => true,
                    ],
                ];
            }

            if ($pattern['type'] === 'category_comparison') {
                return [
                    'type' => 'bar',
                    'reason' => 'Grouped bar charts are excellent for comparing values across multiple categories',
                    'config' => [
                        'xAxis' => $pattern['category_column'],
                        'yAxis' => $pattern['metric_columns'][0] ?? null,
                        'series' => $pattern['grouping_column'],
                        'comparison' => true,
                    ],
                ];
            }
        }

        // Time series data
        if ($characteristics['has_time_series'] && $characteristics['has_numeric_data']) {
            return [
                'type' => 'line',
                'reason' => 'Line charts are ideal for showing trends over time',
                'config' => [
                    'xAxis' => $characteristics['time_columns'][0] ?? null,
                    'yAxis' => $characteristics['numeric_columns'][0] ?? null,
                ],
            ];
        }

        // Categorical comparisons
        if ($characteristics['has_categories'] && $characteristics['has_numeric_data']) {
            // For aggregated queries (like totals by month), always use bar chart
            if ($characteristics['row_count'] < 20 && ! empty($characteristics['categorical_columns'])) {
                return [
                    'type' => 'pie',
                    'reason' => 'Pie charts work well for showing parts of a whole with few categories',
                    'config' => [
                        'dimension' => $characteristics['categorical_columns'][0] ?? null,
                        'metric' => $characteristics['numeric_columns'][0] ?? null,
                    ],
                ];
            } else {
                return [
                    'type' => 'bar',
                    'reason' => 'Bar charts are excellent for comparing values across categories',
                    'config' => [
                        'xAxis' => $characteristics['categorical_columns'][0] ?? null,
                        'yAxis' => $characteristics['numeric_columns'][0] ?? null,
                    ],
                ];
            }
        }

        // Multiple numeric columns - scatter plot
        if (count($characteristics['numeric_columns']) >= 2) {
            return [
                'type' => 'scatter',
                'reason' => 'Scatter plots reveal relationships between numeric variables',
                'config' => [
                    'xAxis' => $characteristics['numeric_columns'][0],
                    'yAxis' => $characteristics['numeric_columns'][1],
                ],
            ];
        }

        // Default to table
        return [
            'type' => 'table',
            'reason' => 'Tables provide a detailed view of all data',
            'config' => [],
        ];
    }

    protected function getAlternativeVisualizations(array $characteristics): array
    {
        $alternatives = [];

        if ($characteristics['has_time_series'] && $characteristics['has_numeric_data']) {
            $alternatives[] = ['type' => 'area', 'reason' => 'Area charts emphasize magnitude of change'];
            $alternatives[] = ['type' => 'bar', 'reason' => 'Bar charts can show discrete time periods'];
        }

        if ($characteristics['has_categories'] && $characteristics['has_numeric_data']) {
            $alternatives[] = ['type' => 'horizontal-bar', 'reason' => 'Horizontal bars work well for long category names'];
            if (count($characteristics['numeric_columns']) > 1) {
                $alternatives[] = ['type' => 'grouped-bar', 'reason' => 'Compare multiple metrics across categories'];
            }
        }

        if (count($characteristics['numeric_columns']) >= 2) {
            $alternatives[] = ['type' => 'heatmap', 'reason' => 'Heatmaps show patterns in multi-dimensional data'];
        }

        // Always include table as an option
        $alternatives[] = ['type' => 'table', 'reason' => 'View raw data in detail'];

        return array_slice($alternatives, 0, 3);
    }

    protected function identifyDimensions(array $columns, array $data): array
    {
        $dimensions = [];

        foreach ($columns as $column) {
            $columnName = $column['name'];
            $sampleValues = array_slice(array_column($data, $columnName), 0, 10);

            if (! $this->isNumericColumn($column['type'], $sampleValues) ||
                $this->isTimeColumn($columnName, $column['type'], $sampleValues)) {
                $dimensions[] = $columnName;
            }
        }

        return $dimensions;
    }

    protected function identifyMetrics(array $columns, array $data): array
    {
        $metrics = [];

        foreach ($columns as $column) {
            $columnName = $column['name'];
            $sampleValues = array_slice(array_column($data, $columnName), 0, 10);

            if ($this->isNumericColumn($column['type'], $sampleValues) &&
                ! $this->isTimeColumn($columnName, $column['type'], $sampleValues)) {
                $metrics[] = $columnName;
            }
        }

        return $metrics;
    }

    protected function isTimeColumn(string $name, string $type, array $sampleValues): bool
    {
        // Check column name patterns
        $timePatterns = ['date', 'time', 'created', 'updated', 'timestamp', '_at', 'year', 'month', 'day'];
        foreach ($timePatterns as $pattern) {
            if (stripos($name, $pattern) !== false) {
                return true;
            }
        }

        // Check data type
        if (in_array(strtolower($type), ['date', 'datetime', 'timestamp'])) {
            return true;
        }

        // Check sample values for date patterns
        foreach ($sampleValues as $value) {
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return true;
            }
        }

        return false;
    }

    protected function isNumericColumn(string $type, array $sampleValues): bool
    {
        // Check data type
        $numericTypes = ['int', 'integer', 'float', 'double', 'decimal', 'numeric', 'real', 'smallint', 'bigint'];
        foreach ($numericTypes as $numericType) {
            if (stripos($type, $numericType) !== false) {
                return true;
            }
        }

        // Check sample values
        $numericCount = 0;
        foreach ($sampleValues as $value) {
            if (is_numeric($value)) {
                $numericCount++;
            }
        }

        return $numericCount > count($sampleValues) * 0.8;
    }

    protected function generateAxisBasedConfig(array $dimensions, array $metrics, string $type): array
    {
        return [
            'chartType' => $type,
            'xAxis' => $dimensions[0] ?? null,
            'yAxis' => $metrics[0] ?? null,
            'series' => array_slice($metrics, 0, 3), // Limit to 3 series
        ];
    }

    protected function generatePieConfig(array $dimensions, array $metrics): array
    {
        return [
            'chartType' => 'pie',
            'dimension' => $dimensions[0] ?? null,
            'metric' => $metrics[0] ?? null,
        ];
    }

    protected function generateScatterConfig(array $dimensions, array $metrics): array
    {
        return [
            'chartType' => 'scatter',
            'xAxis' => $metrics[0] ?? $dimensions[0] ?? null,
            'yAxis' => $metrics[1] ?? $dimensions[1] ?? null,
            'sizeAxis' => $metrics[2] ?? null,
            'colorAxis' => $dimensions[0] ?? null,
        ];
    }

    protected function generateTableConfig(array $columns): array
    {
        return [
            'chartType' => 'table',
            'columns' => array_map(function ($col) {
                return [
                    'field' => $col['name'],
                    'header' => ucwords(str_replace('_', ' ', $col['name'])),
                    'type' => $col['type'],
                ];
            }, $columns),
        ];
    }

    protected function getDefaultVisualization(): array
    {
        return [
            'success' => false,
            'recommendation' => [
                'type' => 'table',
                'reason' => 'Unable to determine best visualization, showing data as table',
                'config' => [],
            ],
            'alternatives' => [],
        ];
    }

    protected function detectComparisonPattern(array $data, array $columns, array $characteristics): ?array
    {
        if (empty($data)) {
            return null;
        }

        // Check for year column (common comparison dimension)
        $yearColumn = null;
        $monthColumn = null;
        foreach ($columns as $col) {
            $colNameLower = strtolower($col['name']);
            if ($colNameLower === 'year' || str_contains($colNameLower, 'year')) {
                $yearColumn = $col['name'];
            }
            if ($colNameLower === 'month' || str_contains($colNameLower, 'month')) {
                $monthColumn = $col['name'];
            }
        }

        // Year-over-year comparison pattern
        if ($yearColumn && $monthColumn && ! empty($characteristics['numeric_columns'])) {
            $years = array_unique(array_column($data, $yearColumn));
            if (count($years) > 1) {
                return [
                    'type' => 'year_over_year',
                    'grouping_column' => $yearColumn,
                    'category_column' => $monthColumn,
                    'metric_columns' => $characteristics['numeric_columns'],
                    'groups' => $years,
                ];
            }
        }

        // Category comparison pattern (when there's a repeating category)
        foreach ($characteristics['categorical_columns'] as $catCol) {
            $uniqueValues = array_unique(array_column($data, $catCol));
            if (count($uniqueValues) >= 2 && count($uniqueValues) <= 10) {
                // Check if data repeats for each category
                $otherColumns = array_diff(
                    array_column($columns, 'name'),
                    [$catCol],
                    $characteristics['numeric_columns']
                );

                if (! empty($otherColumns)) {
                    return [
                        'type' => 'category_comparison',
                        'grouping_column' => $catCol,
                        'category_column' => reset($otherColumns),
                        'metric_columns' => $characteristics['numeric_columns'],
                        'groups' => $uniqueValues,
                    ];
                }
            }
        }

        return null;
    }

    public function transformDataForComparison(array $queryResults, array $comparisonPattern): array
    {
        if (! $comparisonPattern) {
            return $queryResults['data'];
        }

        $data = $queryResults['data'];
        $groupingCol = $comparisonPattern['grouping_column'];
        $categoryCol = $comparisonPattern['category_column'];
        $metricCol = $comparisonPattern['metric_columns'][0] ?? null;

        if (! $metricCol) {
            return $data;
        }

        // Transform data into multi-series format
        $categories = array_unique(array_column($data, $categoryCol));
        $groups = $comparisonPattern['groups'];

        // Build series data
        $series = [];
        foreach ($groups as $group) {
            $seriesData = [];
            foreach ($categories as $category) {
                // Find the value for this group/category combination
                $value = 0;
                foreach ($data as $row) {
                    if ($row[$groupingCol] == $group && $row[$categoryCol] == $category) {
                        $value = floatval($row[$metricCol]);
                        break;
                    }
                }
                $seriesData[] = $value;
            }

            $series[] = [
                'name' => (string) $group,
                'data' => $seriesData,
            ];
        }

        return [
            'categories' => array_map('strval', $categories),
            'series' => $series,
        ];
    }
}
