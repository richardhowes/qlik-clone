<?php

namespace App\Services\DataSource\Connectors;

use Exception;
use PDO;

class PostgreSQLConnector implements ConnectorInterface
{
    public function test(array $config): array
    {
        try {
            $pdo = $this->getConnection($config);
            $pdo->query('SELECT 1');

            return [
                'success' => true,
                'message' => 'Connection successful',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getSchema(array $config): array
    {
        try {
            $pdo = $this->getConnection($config);

            // Get tables
            $stmt = $pdo->query("
                SELECT 
                    t.table_name,
                    obj_description(pgc.oid, 'pg_class') as table_comment
                FROM information_schema.tables t
                JOIN pg_catalog.pg_class pgc ON pgc.relname = t.table_name
                WHERE t.table_schema = 'public' 
                AND t.table_type = 'BASE TABLE'
                ORDER BY t.table_name
            ");

            $tables = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tableName = $row['table_name'];

                // Get columns for each table
                $colStmt = $pdo->prepare("
                    SELECT 
                        c.column_name,
                        c.data_type,
                        c.is_nullable,
                        c.column_default,
                        col_description(pgc.oid, c.ordinal_position) as column_comment,
                        CASE 
                            WHEN tc.constraint_type = 'PRIMARY KEY' THEN 'PRI'
                            WHEN tc.constraint_type = 'FOREIGN KEY' THEN 'MUL'
                            WHEN tc.constraint_type = 'UNIQUE' THEN 'UNI'
                            ELSE ''
                        END as column_key
                    FROM information_schema.columns c
                    LEFT JOIN information_schema.key_column_usage kcu 
                        ON c.table_name = kcu.table_name 
                        AND c.column_name = kcu.column_name
                    LEFT JOIN information_schema.table_constraints tc 
                        ON kcu.constraint_name = tc.constraint_name
                    JOIN pg_catalog.pg_class pgc 
                        ON pgc.relname = c.table_name
                    WHERE c.table_schema = 'public' 
                    AND c.table_name = ?
                    ORDER BY c.ordinal_position
                ");
                $colStmt->execute([$tableName]);

                $columns = [];
                while ($col = $colStmt->fetch(PDO::FETCH_ASSOC)) {
                    $columns[] = [
                        'name' => $col['column_name'],
                        'type' => $col['data_type'],
                        'nullable' => $col['is_nullable'] === 'YES',
                        'key' => $col['column_key'],
                        'comment' => $col['column_comment'],
                    ];
                }

                $tables[] = [
                    'name' => $tableName,
                    'comment' => $row['table_comment'],
                    'columns' => $columns,
                ];
            }

            return [
                'tables' => $tables,
                'error' => false,
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function query(array $config, string $query, array $params = []): array
    {
        try {
            $pdo = $this->getConnection($config);

            // Set a reasonable timeout
            $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $results,
                'rowCount' => count($results),
                'error' => false,
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'host',
                'label' => 'Host',
                'type' => 'text',
                'required' => true,
                'default' => 'localhost',
            ],
            [
                'name' => 'port',
                'label' => 'Port',
                'type' => 'number',
                'required' => true,
                'default' => '5432',
            ],
            [
                'name' => 'database',
                'label' => 'Database',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'username',
                'label' => 'Username',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'required' => false,
            ],
            [
                'name' => 'schema',
                'label' => 'Schema',
                'type' => 'text',
                'required' => false,
                'default' => 'public',
            ],
            [
                'name' => 'sslmode',
                'label' => 'SSL Mode',
                'type' => 'select',
                'options' => [
                    ['value' => 'disable', 'label' => 'Disable'],
                    ['value' => 'require', 'label' => 'Require'],
                    ['value' => 'verify-ca', 'label' => 'Verify CA'],
                    ['value' => 'verify-full', 'label' => 'Verify Full'],
                ],
                'required' => false,
                'default' => 'disable',
            ],
        ];
    }

    protected function getConnection(array $config): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['host'] ?? 'localhost',
            $config['port'] ?? '5432',
            $config['database']
        );

        if (! empty($config['sslmode']) && $config['sslmode'] !== 'disable') {
            $dsn .= ';sslmode='.$config['sslmode'];
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO(
            $dsn,
            $config['username'],
            $config['password'] ?? '',
            $options
        );
    }
}
