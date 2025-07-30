<?php

namespace App\Services\Query;

use App\Models\DataSource;
use App\Models\Query;
use App\Services\DataSource\ConnectionManager;
use Illuminate\Support\Facades\Log;

class QueryService
{
    protected ConnectionManager $connectionManager;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    public function executeQuery(DataSource $dataSource, string $sql, int $limit = 1000): array
    {
        $startTime = microtime(true);

        try {
            // Decrypt the connection config
            $config = json_decode(\Illuminate\Support\Facades\Crypt::decryptString($dataSource->connection_config), true);

            // Get connector for the data source
            $connector = $this->connectionManager->getConnector($dataSource->type);

            // For analytics queries, we'll use DuckDB in the future
            // For now, execute directly on the source database
            // Add LIMIT to the query if not already present
            if ($limit && ! preg_match('/\bLIMIT\s+\d+/i', $sql)) {
                $sql .= " LIMIT $limit";
            }

            $results = $connector->query($config, $sql);

            $executionTime = (int) ((microtime(true) - $startTime) * 1000); // Convert to milliseconds

            if ($results['error']) {
                throw new \Exception($results['message']);
            }

            return [
                'success' => true,
                'data' => $results['data'],
                'columns' => $this->extractColumns($results['data']),
                'row_count' => count($results['data']),
                'execution_time' => $executionTime,
                'limited' => count($results['data']) >= $limit,
            ];
        } catch (\Exception $e) {
            Log::error('Query execution failed', [
                'data_source_id' => $dataSource->id,
                'sql' => $sql,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $this->sanitizeErrorMessage($e->getMessage()),
                'execution_time' => (int) ((microtime(true) - $startTime) * 1000),
            ];
        }
    }

    public function validateQuery(string $sql): array
    {
        // Basic SQL validation
        $sql = trim($sql);

        if (empty($sql)) {
            return [
                'valid' => false,
                'error' => 'Query cannot be empty',
            ];
        }

        // Check for dangerous statements (this is a basic check, not comprehensive)
        $dangerousKeywords = [
            'DROP', 'DELETE', 'TRUNCATE', 'ALTER', 'CREATE',
            'INSERT', 'UPDATE', 'GRANT', 'REVOKE', 'EXEC', 'EXECUTE',
        ];

        $upperSql = strtoupper($sql);
        foreach ($dangerousKeywords as $keyword) {
            if (preg_match('/\b'.$keyword.'\b/', $upperSql)) {
                return [
                    'valid' => false,
                    'error' => "Query contains restricted keyword: $keyword",
                ];
            }
        }

        return [
            'valid' => true,
        ];
    }

    public function saveQuery(array $data): Query
    {
        return Query::create($data);
    }

    protected function extractColumns(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $firstRow = $data[0];
        $columns = [];

        foreach (array_keys($firstRow) as $key) {
            $columns[] = [
                'name' => $key,
                'type' => $this->guessColumnType($firstRow[$key]),
            ];
        }

        return $columns;
    }

    protected function guessColumnType($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'float';
        }
        if (is_numeric($value)) {
            return 'numeric';
        }
        if (strtotime($value) !== false) {
            return 'datetime';
        }

        return 'string';
    }

    protected function sanitizeErrorMessage(string $message): string
    {
        // Remove sensitive information from error messages
        $patterns = [
            '/at line \d+/' => '',
            '/near ".+"/' => 'near [query]',
            '/password=\S+/' => 'password=***',
            '/host=\S+/' => 'host=***',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $message);
    }
}
