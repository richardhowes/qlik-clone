<?php

namespace App\Services\DataSource\Connectors;

use Exception;
use PDO;
use PDOException;

class MySQLConnector implements ConnectorInterface
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
                SELECT TABLE_NAME, TABLE_COMMENT 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_TYPE = 'BASE TABLE'
                ORDER BY TABLE_NAME
            ");
            
            $tables = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tableName = $row['TABLE_NAME'];
                
                // Get columns for each table
                $colStmt = $pdo->prepare("
                    SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_COMMENT
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                    ORDER BY ORDINAL_POSITION
                ");
                $colStmt->execute([$tableName]);
                
                $columns = [];
                while ($col = $colStmt->fetch(PDO::FETCH_ASSOC)) {
                    $columns[] = [
                        'name' => $col['COLUMN_NAME'],
                        'type' => $col['DATA_TYPE'],
                        'nullable' => $col['IS_NULLABLE'] === 'YES',
                        'key' => $col['COLUMN_KEY'],
                        'comment' => $col['COLUMN_COMMENT'],
                    ];
                }
                
                $tables[] = [
                    'name' => $tableName,
                    'comment' => $row['TABLE_COMMENT'],
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
                'default' => '3306',
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
                'name' => 'ssl',
                'label' => 'Use SSL',
                'type' => 'checkbox',
                'required' => false,
                'default' => false,
            ],
        ];
    }

    protected function getConnection(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'] ?? 'localhost',
            $config['port'] ?? '3306',
            $config['database']
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        if (!empty($config['ssl'])) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = true;
        }
        
        return new PDO(
            $dsn,
            $config['username'],
            $config['password'] ?? '',
            $options
        );
    }
}