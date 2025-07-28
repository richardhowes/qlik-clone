<?php

namespace App\Services\DataSource\Connectors;

interface ConnectorInterface
{
    /**
     * Test the connection with the given configuration.
     *
     * @param array $config
     * @return array ['success' => bool, 'message' => string]
     */
    public function test(array $config): array;

    /**
     * Get the database schema (tables and columns).
     *
     * @param array $config
     * @return array
     */
    public function getSchema(array $config): array;

    /**
     * Execute a query and return results.
     *
     * @param array $config
     * @param string $query
     * @param array $params
     * @return array
     */
    public function query(array $config, string $query, array $params = []): array;

    /**
     * Get the connection configuration fields required for this connector.
     *
     * @return array
     */
    public function getConfigFields(): array;
}