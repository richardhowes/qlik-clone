<?php

namespace App\Services\DataSource\Connectors;

interface ConnectorInterface
{
    /**
     * Test the connection with the given configuration.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function test(array $config): array;

    /**
     * Get the database schema (tables and columns).
     */
    public function getSchema(array $config): array;

    /**
     * Execute a query and return results.
     */
    public function query(array $config, string $query, array $params = []): array;

    /**
     * Get the connection configuration fields required for this connector.
     */
    public function getConfigFields(): array;
}
