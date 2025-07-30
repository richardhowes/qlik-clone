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

        return Cache::remember($cacheKey, 300, function () use ($question, $dataSource) {
            try {
                $schema = $this->schemaAnalyzer->getSchemaContext($dataSource);

                $prompt = $this->buildTranslationPrompt($question, $schema);

                $response = Prism::text()
                    ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                    ->withPrompt($prompt)
                    ->withMaxTokens(500)
                    ->withSystemPrompt('You are a SQL expert. Generate only valid SQL queries without explanations.')
                    ->asText();

                $sql = $this->extractSQL($response->text);

                $validation = $this->validateGeneratedSQL($sql);
                if (! $validation['valid']) {
                    throw new \Exception($validation['error']);
                }

                return [
                    'success' => true,
                    'sql' => $sql,
                    'explanation' => $this->generateQueryExplanation($question, $sql),
                ];
            } catch (\Exception $e) {
                Log::error('AI Query translation failed', [
                    'question' => $question,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to translate your question. Please ask questions about your data, like "Show me total sales" or "Count all customers".',
                ];
            }
        });
    }

    public function suggestFollowUpQuestions(string $originalQuestion, array $queryResults): array
    {
        try {
            $prompt = $this->buildFollowUpPrompt($originalQuestion, $queryResults);

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
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

Requirements:
- Use only the tables and columns available in the schema
- Include appropriate JOINs if multiple tables are needed
- Use aggregate functions where appropriate
- Return only the SQL query without any explanation
- Do NOT include semicolon at the end
- Do NOT add LIMIT clause (it will be added automatically)
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
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
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
}
