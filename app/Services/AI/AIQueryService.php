<?php

namespace App\Services\AI;

use App\Models\DataSource;
use App\Services\Query\QueryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AIQueryService
{
    protected QueryService $queryService;

    protected SchemaAnalyzer $schemaAnalyzer;

    public function __construct(QueryService $queryService, SchemaAnalyzer $schemaAnalyzer)
    {
        $this->queryService = $queryService;
        $this->schemaAnalyzer = $schemaAnalyzer;
    }

    public function translateNaturalLanguageToSQL(string $question, DataSource $dataSource): array
    {
        $cacheKey = 'ai_query:'.md5($question.$dataSource->id);

        // For debugging, let's skip cache temporarily
        // return Cache::remember($cacheKey, 300, function () use ($question, $dataSource) {
        try {
            Log::info('Starting AI query translation', [
                'question' => $question,
                'data_source_id' => $dataSource->id,
            ]);

            $schema = $this->schemaAnalyzer->getSchemaContext($dataSource);

            Log::info('Schema context retrieved', [
                'table_count' => count($schema),
                'tables' => array_keys($schema),
            ]);

            // If schema is empty, provide a helpful error
            if (empty($schema)) {
                throw new \Exception('No schema information available for this data source. Please ensure the data source is properly connected.');
            }

            $prompt = $this->buildTranslationPrompt($question, $schema);

            Log::info('Sending prompt to AI', [
                'prompt_length' => strlen($prompt),
            ]);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withPrompt($prompt)
                ->withMaxTokens(500)
                ->withSystemPrompt('You are a SQL expert. Generate only valid SQL queries without explanations.')
                ->asText();

            $extracted = $this->extractSQLAndTitle($response->text);
            $sql = $extracted['sql'];
            $title = $extracted['title'];

            Log::info('SQL and title generated', [
                'sql' => $sql,
                'title' => $title,
            ]);

            $validation = $this->validateGeneratedSQL($sql);
            if (! $validation['valid']) {
                throw new \Exception($validation['error']);
            }

            return [
                'success' => true,
                'sql' => $sql,
                'title' => $title,
                'explanation' => $this->generateQueryExplanation($question, $sql),
            ];
        } catch (\Exception $e) {
            Log::error('AI Query translation failed', [
                'question' => $question,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Provide more specific error messages
            $errorMessage = 'Failed to translate your question.';

            if (str_contains($e->getMessage(), 'cURL error 6') || str_contains($e->getMessage(), 'Could not resolve host')) {
                // Try to provide a basic fallback query
                $fallbackResult = $this->generateFallbackQuery($question, $schema);
                if ($fallbackResult['success']) {
                    Log::info('Using fallback query due to network issues');

                    return $fallbackResult;
                }
                $errorMessage = 'Unable to connect to AI service. Please check your internet connection.';
            } elseif (str_contains($e->getMessage(), 'No schema information')) {
                $errorMessage = $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'API key')) {
                $errorMessage = 'OpenAI API key is not configured. Please check your configuration.';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }
        // });
    }

    public function suggestFollowUpQuestions(string $originalQuestion, array $queryResults): array
    {
        try {
            $prompt = $this->buildFollowUpPrompt($originalQuestion, $queryResults);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withPrompt($prompt)
                ->withMaxTokens(300)
                ->asText();

            return $this->parseFollowUpQuestions($response->text);
        } catch (\Exception $e) {
            Log::error('Failed to generate follow-up questions', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function buildTranslationPrompt(string $question, array $schema): string
    {
        $schemaDescription = $this->formatSchemaForPrompt($schema);

        return <<<PROMPT
Given the following database schema:

$schemaDescription

Generate a SQL query to answer this question: "$question"

Also provide a concise chart title (max 50 characters) that clearly describes what the data shows.

Requirements:
- Use only the tables and columns available in the schema
- Include appropriate JOINs if multiple tables are needed
- Use aggregate functions where appropriate
- For date-based queries:
  - "last month" means the previous calendar month (use DATE_SUB or appropriate date functions)
  - "this month" means the current calendar month
  - "last year" means the previous calendar year
- For revenue/amount calculations, look for columns containing: amount, total, price, revenue, value
- Return the SQL query and chart title in this exact format:
  SQL: [your query here]
  TITLE: [your title here]
- Do NOT include semicolon at the end of SQL
- Do NOT add LIMIT clause (it will be added automatically)
- Make reasonable assumptions about column meanings based on their names

IMPORTANT - For comparison queries:
- When comparing different time periods (e.g., "2023 vs 2024", "compare X to Y"), include ALL dimensions in SELECT
- For year-over-year comparisons: SELECT year, month, metric ORDER BY month, year
- For month-over-month: SELECT month, year, metric ORDER BY year, month
- Include the comparison dimension (year, category, etc.) as a separate column
- Example: "compare 2023 to 2024" should return: SELECT YEAR(date) as year, MONTH(date) as month, SUM(revenue) as total FROM table WHERE YEAR(date) IN (2023, 2024) GROUP BY year, month ORDER BY month, year

Example patterns:
- For "revenue last month": SUM columns related to money/amounts where date is in previous month
- For "count of X": COUNT(*) or COUNT(DISTINCT column) as appropriate
- For "average X": AVG(column) for numeric values
- For "compare 2023 to 2024": Include year as a grouping column, filter for both years
- For "X vs Y": Structure data to show both X and Y as separate series

Title examples:
- "Monthly Revenue Comparison: 2023 vs 2024"
- "Top Products by Sales Volume"
- "Customer Growth Trend"
- "Revenue by Region"
PROMPT;
    }

    protected function formatSchemaForPrompt(array $schema): string
    {
        $formatted = '';
        foreach ($schema as $table => $columns) {
            $formatted .= "Table: $table\n";
            $formatted .= 'Columns: '.implode(', ', array_map(function ($col) {
                return $col['name'].' ('.$col['type'].')';
            }, $columns))."\n\n";
        }

        return $formatted;
    }

    protected function extractSQL(string $response): string
    {
        // Remove markdown code blocks if present
        $sql = preg_replace('/```sql\s*\n?/', '', $response);
        $sql = preg_replace('/```\s*\n?/', '', $sql);

        // Trim and clean up
        $sql = trim($sql);

        // Remove any existing semicolon to avoid issues with LIMIT clause
        $sql = rtrim($sql, ';');

        return $sql;
    }

    protected function extractSQLAndTitle(string $response): array
    {
        $sql = '';
        $title = '';

        // Log the raw response for debugging
        Log::info('Raw AI response for SQL extraction', ['response' => $response]);

        // Try to extract SQL and TITLE from the formatted response
        // Look for SQL: followed by the query, ending before TITLE: or end of string
        if (preg_match('/SQL:\s*(.+?)(?=TITLE:|$)/si', $response, $sqlMatch)) {
            $sql = trim($sqlMatch[1]);
        }

        // Look for TITLE: followed by the title
        if (preg_match('/TITLE:\s*(.+?)$/mi', $response, $titleMatch)) {
            $title = trim($titleMatch[1]);
        }

        // If SQL still contains TITLE, extract it again more carefully
        if (stripos($sql, 'TITLE:') !== false) {
            $parts = preg_split('/TITLE:/i', $sql, 2);
            $sql = trim($parts[0]);
            if (empty($title) && isset($parts[1])) {
                $title = trim($parts[1]);
            }
        }

        // Fallback to old method if new format not found
        if (empty($sql)) {
            $sql = $this->extractSQL($response);
        }

        // Clean up SQL
        $sql = preg_replace('/```sql\s*\n?/', '', $sql);
        $sql = preg_replace('/```\s*\n?/', '', $sql);
        $sql = trim($sql);
        $sql = rtrim($sql, ';');

        // Remove any remaining TITLE: text from SQL
        $sql = preg_replace('/\s*TITLE:.*$/i', '', $sql);

        // Clean up title
        $title = str_replace(['"', "'", '`'], '', $title);
        $title = trim($title);

        // Ensure title is not too long
        if (strlen($title) > 60) {
            $title = substr($title, 0, 57) . '...';
        }

        Log::info('Extracted SQL and title', [
            'sql' => $sql,
            'title' => $title,
        ]);

        return [
            'sql' => $sql,
            'title' => $title ?: 'Query Results',
        ];
    }

    protected function validateGeneratedSQL(string $sql): array
    {
        // Use existing validation from QueryService
        $validation = $this->queryService->validateQuery($sql);

        // Additional AI-specific validation
        if ($validation['valid']) {
            // Check for multiple statements
            if (substr_count($sql, ';') > 1) {
                return [
                    'valid' => false,
                    'error' => 'Multiple SQL statements detected',
                ];
            }

            // Ensure SELECT query
            if (! preg_match('/^\s*SELECT/i', $sql)) {
                return [
                    'valid' => false,
                    'error' => 'Only SELECT queries are allowed',
                ];
            }
        }

        return $validation;
    }

    protected function generateQueryExplanation(string $question, string $sql): string
    {
        try {
            $prompt = "Explain in simple terms what this SQL query does to answer the question '$question':\n\n$sql\n\nKeep the explanation brief and user-friendly.";

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withPrompt($prompt)
                ->withMaxTokens(150)
                ->asText();

            return $response->text;
        } catch (\Exception $e) {
            return 'This query retrieves data to answer your question.';
        }
    }

    protected function buildFollowUpPrompt(string $originalQuestion, array $results): string
    {
        $resultSummary = $this->summarizeResults($results);

        return <<<PROMPT
Based on this question: "$originalQuestion"
And these results: $resultSummary

Suggest 3 follow-up questions that would provide additional insights.
Format as a simple numbered list without explanations.
PROMPT;
    }

    protected function summarizeResults(array $results): string
    {
        if (empty($results['data'])) {
            return 'No results found';
        }

        $rowCount = $results['row_count'] ?? count($results['data']);
        $columns = array_keys($results['data'][0] ?? []);

        return "Found $rowCount rows with columns: ".implode(', ', $columns);
    }

    protected function parseFollowUpQuestions(string $response): array
    {
        $questions = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            if (preg_match('/^\d+\.\s*(.+)$/', trim($line), $matches)) {
                $questions[] = trim($matches[1]);
            }
        }

        return array_slice($questions, 0, 3);
    }

    protected function generateFallbackQuery(string $question, array $schema): array
    {
        try {
            // Basic pattern matching for common queries
            $questionLower = strtolower($question);

            // Find reservation-related tables
            $reservationTables = array_filter(array_keys($schema), function ($table) {
                return str_contains(strtolower($table), 'reserv');
            });

            if (empty($reservationTables)) {
                return ['success' => false];
            }

            $mainTable = reset($reservationTables); // Get first reservation table
            $columns = $schema[$mainTable] ?? [];

            // Look for revenue/amount columns
            $revenueColumns = [];
            foreach ($columns as $col) {
                $colName = strtolower($col['name']);
                if (str_contains($colName, 'revenue') ||
                    str_contains($colName, 'amount') ||
                    str_contains($colName, 'total') ||
                    str_contains($colName, 'price')) {
                    $revenueColumns[] = $col['name'];
                }
            }

            // Look for date columns
            $dateColumns = [];
            foreach ($columns as $col) {
                $colName = strtolower($col['name']);
                if (str_contains($colName, 'date') ||
                    str_contains($colName, 'created') ||
                    str_contains($colName, 'time')) {
                    $dateColumns[] = $col['name'];
                }
            }

            // Generate a basic query based on patterns
            if (str_contains($questionLower, 'revenue') &&
                str_contains($questionLower, 'last month') &&
                ! empty($revenueColumns) &&
                ! empty($dateColumns)) {

                $revenueCol = $revenueColumns[0];
                $dateCol = $dateColumns[0];

                $sql = "SELECT SUM($revenueCol) as total_revenue
FROM $mainTable
WHERE $dateCol >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)
AND $dateCol < CURRENT_DATE";

                return [
                    'success' => true,
                    'sql' => $sql,
                    'title' => 'Last Month Revenue',
                    'explanation' => 'This query calculates the total revenue from the last month. (Note: This is a basic query generated offline - the AI service is currently unavailable)',
                ];
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('Fallback query generation failed', [
                'error' => $e->getMessage(),
            ]);

            return ['success' => false];
        }
    }
}
