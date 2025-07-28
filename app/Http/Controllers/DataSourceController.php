<?php

namespace App\Http\Controllers;

use App\Models\DataSource;
use App\Services\DataSource\ConnectionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use Inertia\Response;

class DataSourceController extends Controller
{
    protected ConnectionManager $connectionManager;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    public function index(): Response
    {
        $dataSources = DataSource::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('data-sources/Index', [
            'dataSources' => $dataSources,
        ]);
    }

    public function create(): Response
    {
        $types = [
            ['value' => 'mysql', 'label' => 'MySQL'],
            ['value' => 'mariadb', 'label' => 'MariaDB'],
            ['value' => 'postgresql', 'label' => 'PostgreSQL'],
        ];

        return Inertia::render('data-sources/Create', [
            'types' => $types,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mysql,mariadb,postgresql',
            'connection_config' => 'required|array',
            'test_only' => 'boolean',
        ]);

        // If this is just a test, validate and test the connection
        if ($request->input('test_only', false)) {
            // Create a temporary data source object for testing
            $tempDataSource = new DataSource([
                'type' => $validated['type'],
                'connection_config' => Crypt::encryptString(json_encode($validated['connection_config'])),
            ]);

            $result = $this->connectionManager->testConnection($tempDataSource);

            return back()->with('testResult', $result);
        }

        // Otherwise, create the data source
        $validated['connection_config'] = Crypt::encryptString(json_encode($validated['connection_config']));
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'inactive';

        $dataSource = DataSource::create($validated);

        // Test connection immediately
        $this->connectionManager->testConnection($dataSource);

        return redirect()->route('data-sources.index')
            ->with('success', 'Data source created successfully');
    }

    public function show(DataSource $dataSource): Response
    {
        $this->authorize('view', $dataSource);

        // Get schema information
        $schema = $this->connectionManager->getSchema($dataSource);

        return Inertia::render('data-sources/Show', [
            'dataSource' => $dataSource,
            'schema' => $schema,
        ]);
    }

    public function edit(DataSource $dataSource): Response
    {
        $this->authorize('update', $dataSource);

        $types = [
            ['value' => 'mysql', 'label' => 'MySQL'],
            ['value' => 'mariadb', 'label' => 'MariaDB'],
            ['value' => 'postgresql', 'label' => 'PostgreSQL'],
        ];

        // Decrypt config for editing
        $dataSource->connection_config = json_decode(
            Crypt::decryptString($dataSource->connection_config),
            true
        );

        return Inertia::render('data-sources/Edit', [
            'dataSource' => $dataSource,
            'types' => $types,
        ]);
    }

    public function update(Request $request, DataSource $dataSource)
    {
        $this->authorize('update', $dataSource);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'connection_config' => 'required|array',
            'test_only' => 'boolean',
        ]);

        // If this is just a test, validate and test the connection
        if ($request->input('test_only', false)) {
            // Create a temporary data source object for testing
            $tempDataSource = clone $dataSource;
            $tempDataSource->connection_config = Crypt::encryptString(json_encode($validated['connection_config']));

            $result = $this->connectionManager->testConnection($tempDataSource);

            return back()->with('testResult', $result);
        }

        // Encrypt connection config
        $validated['connection_config'] = Crypt::encryptString(json_encode($validated['connection_config']));

        $dataSource->update($validated);

        return redirect()->route('data-sources.index')
            ->with('success', 'Data source updated successfully');
    }

    public function destroy(DataSource $dataSource)
    {
        $this->authorize('delete', $dataSource);

        $dataSource->delete();

        return redirect()->route('data-sources.index')
            ->with('success', 'Data source deleted successfully');
    }

    public function test(DataSource $dataSource)
    {
        $this->authorize('update', $dataSource);

        $result = $this->connectionManager->testConnection($dataSource);

        return back()->with('flash', [
            'type' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }


    public function getConfigFields(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
        ]);

        try {
            $connector = $this->connectionManager->getConnector($request->type);
            return response()->json([
                'fields' => $connector->getConfigFields(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
