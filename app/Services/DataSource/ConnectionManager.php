<?php

namespace App\Services\DataSource;

use App\Models\DataSource;
use App\Services\DataSource\Connectors\ConnectorInterface;
use App\Services\DataSource\Connectors\MySQLConnector;
use App\Services\DataSource\Connectors\PostgreSQLConnector;
use Exception;
use Illuminate\Support\Facades\Crypt;

class ConnectionManager
{
    protected array $connectors = [];

    public function __construct()
    {
        $this->registerConnectors();
    }

    protected function registerConnectors(): void
    {
        $this->connectors = [
            'mysql' => MySQLConnector::class,
            'mariadb' => MySQLConnector::class,
            'postgresql' => PostgreSQLConnector::class,
        ];
    }

    public function getConnector(string $type): ConnectorInterface
    {
        if (!isset($this->connectors[$type])) {
            throw new Exception("Unsupported data source type: {$type}");
        }

        $connectorClass = $this->connectors[$type];
        return new $connectorClass();
    }

    public function testConnection(DataSource $dataSource): array
    {
        try {
            $connector = $this->getConnector($dataSource->type);
            $config = is_string($dataSource->connection_config) 
                ? json_decode(Crypt::decryptString($dataSource->connection_config), true) 
                : $dataSource->connection_config;
            
            $result = $connector->test($config);
            
            if ($result['success']) {
                $dataSource->update([
                    'status' => 'active',
                    'last_sync_at' => now(),
                ]);
            } else {
                $dataSource->update(['status' => 'error']);
            }
            
            return $result;
        } catch (Exception $e) {
            $dataSource->update(['status' => 'error']);
            
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getSchema(DataSource $dataSource): array
    {
        try {
            $connector = $this->getConnector($dataSource->type);
            $config = is_string($dataSource->connection_config) 
                ? json_decode(Crypt::decryptString($dataSource->connection_config), true) 
                : $dataSource->connection_config;
            
            return $connector->getSchema($config);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'Failed to retrieve schema: ' . $e->getMessage(),
            ];
        }
    }

    public function executeQuery(DataSource $dataSource, string $query, array $params = []): array
    {
        try {
            $connector = $this->getConnector($dataSource->type);
            $config = is_string($dataSource->connection_config) 
                ? json_decode(Crypt::decryptString($dataSource->connection_config), true) 
                : $dataSource->connection_config;
            
            return $connector->query($config, $query, $params);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'Query execution failed: ' . $e->getMessage(),
            ];
        }
    }
}