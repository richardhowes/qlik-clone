<?php

namespace App\Services\AI;

use App\Models\DataSource;
use App\Services\DataSource\ConnectionManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SchemaAnalyzer
{
    protected ConnectionManager $connectionManager;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    public function getSchemaContext(DataSource $dataSource): array
    {
        $cacheKey = 'schema:'.$dataSource->id;

        return Cache::remember($cacheKey, 3600, function () use ($dataSource) {
            try {
                $config = json_decode(Crypt::decryptString($dataSource->connection_config), true);
                $connector = $this->connectionManager->getConnector($dataSource->type);

                // Get table list
                $tables = $this->getTables($connector, $config, $dataSource->type);

                $schema = [];
                
                // For now, just use all tables - you've already limited your dataset
                foreach ($tables as $table) {
                    $schema[$table] = $this->getTableColumns($connector, $config, $table, $dataSource->type);
                }

                return $schema;
            } catch (\Exception $e) {
                Log::error('Schema analysis failed', [
                    'data_source_id' => $dataSource->id,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    public function getTableRelationships(DataSource $dataSource): array
    {
        $cacheKey = 'relationships:'.$dataSource->id;

        return Cache::remember($cacheKey, 3600, function () use ($dataSource) {
            try {
                $config = json_decode(Crypt::decryptString($dataSource->connection_config), true);
                $connector = $this->connectionManager->getConnector($dataSource->type);

                // Get foreign key relationships
                return $this->detectRelationships($connector, $config, $dataSource->type);
            } catch (\Exception $e) {
                Log::error('Relationship detection failed', [
                    'data_source_id' => $dataSource->id,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    public function getSampleData(DataSource $dataSource, string $table, int $limit = 5): array
    {
        try {
            $config = json_decode(Crypt::decryptString($dataSource->connection_config), true);
            $connector = $this->connectionManager->getConnector($dataSource->type);

            $sql = "SELECT * FROM $table LIMIT $limit";
            $result = $connector->query($config, $sql);

            return $result['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to get sample data', [
                'table' => $table,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function getTables($connector, array $config, string $type): array
    {
        $sql = match ($type) {
            'mysql', 'mariadb' => 'SHOW TABLES',
            'postgresql' => "SELECT tablename FROM pg_tables WHERE schemaname = 'public'",
            'sqlite' => "SELECT name FROM sqlite_master WHERE type='table'",
            default => throw new \Exception("Unsupported database type: $type"),
        };

        $result = $connector->query($config, $sql);

        if ($result['error']) {
            throw new \Exception($result['message']);
        }

        return array_column($result['data'], array_keys($result['data'][0])[0]);
    }

    protected function getTableColumns($connector, array $config, string $table, string $type): array
    {
        $sql = match ($type) {
            'mysql', 'mariadb' => "SHOW COLUMNS FROM $table",
            'postgresql' => "SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = '$table'",
            'sqlite' => "PRAGMA table_info($table)",
            default => throw new \Exception("Unsupported database type: $type"),
        };

        $result = $connector->query($config, $sql);

        if ($result['error']) {
            throw new \Exception($result['message']);
        }

        return $this->normalizeColumns($result['data'], $type);
    }

    protected function normalizeColumns(array $columns, string $type): array
    {
        $normalized = [];

        foreach ($columns as $column) {
            switch ($type) {
                case 'mysql':
                case 'mariadb':
                    $normalized[] = [
                        'name' => $column['Field'],
                        'type' => $this->extractDataType($column['Type']),
                        'nullable' => $column['Null'] === 'YES',
                        'key' => $column['Key'] ?? null,
                    ];
                    break;

                case 'postgresql':
                    $normalized[] = [
                        'name' => $column['column_name'],
                        'type' => $column['data_type'],
                        'nullable' => $column['is_nullable'] === 'YES',
                    ];
                    break;

                case 'sqlite':
                    $normalized[] = [
                        'name' => $column['name'],
                        'type' => $column['type'],
                        'nullable' => $column['notnull'] === 0,
                        'key' => $column['pk'] ? 'PRI' : null,
                    ];
                    break;
            }
        }

        return $normalized;
    }

    protected function extractDataType(string $typeDefinition): string
    {
        // Extract base type from definitions like "varchar(255)" or "int(11)"
        if (preg_match('/^(\w+)/', $typeDefinition, $matches)) {
            return $matches[1];
        }

        return $typeDefinition;
    }

    protected function detectRelationships($connector, array $config, string $type): array
    {
        $relationships = [];

        try {
            $sql = match ($type) {
                'mysql', 'mariadb' => '
                    SELECT 
                        TABLE_NAME,
                        COLUMN_NAME,
                        REFERENCED_TABLE_NAME,
                        REFERENCED_COLUMN_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_NAME IS NOT NULL
                        AND TABLE_SCHEMA = DATABASE()
                ',
                'postgresql' => "
                    SELECT
                        tc.table_name,
                        kcu.column_name,
                        ccu.table_name AS referenced_table_name,
                        ccu.column_name AS referenced_column_name
                    FROM information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                        ON tc.constraint_name = kcu.constraint_name
                    JOIN information_schema.constraint_column_usage AS ccu
                        ON ccu.constraint_name = tc.constraint_name
                    WHERE tc.constraint_type = 'FOREIGN KEY'
                ",
                default => null,
            };

            if ($sql) {
                $result = $connector->query($config, $sql);

                if (! $result['error']) {
                    foreach ($result['data'] as $row) {
                        $relationships[] = [
                            'from_table' => $row['TABLE_NAME'] ?? $row['table_name'],
                            'from_column' => $row['COLUMN_NAME'] ?? $row['column_name'],
                            'to_table' => $row['REFERENCED_TABLE_NAME'] ?? $row['referenced_table_name'],
                            'to_column' => $row['REFERENCED_COLUMN_NAME'] ?? $row['referenced_column_name'],
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to detect relationships', ['error' => $e->getMessage()]);
        }

        return $relationships;
    }
}
