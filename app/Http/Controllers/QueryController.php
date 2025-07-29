<?php

namespace App\Http\Controllers;

use App\Models\DataSource;
use App\Models\Query;
use App\Services\Query\QueryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QueryController extends Controller
{
    protected QueryService $queryService;

    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    public function index(DataSource $dataSource): Response
    {
        $this->authorize('view', $dataSource);

        $queries = Query::where('data_source_id', $dataSource->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return Inertia::render('query/Index', [
            'dataSource' => $dataSource,
            'queries' => $queries,
        ]);
    }

    public function editor(DataSource $dataSource, Query $query = null): Response
    {
        $this->authorize('view', $dataSource);
        
        if ($query) {
            $this->authorize('view', $query);
        }

        // Get schema for auto-completion
        $schema = app(\App\Services\DataSource\ConnectionManager::class)->getSchema($dataSource);

        return Inertia::render('query/Editor', [
            'dataSource' => $dataSource,
            'query' => $query,
            'schema' => $schema,
            'recentQueries' => Query::where('data_source_id', $dataSource->id)
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get(['id', 'name', 'created_at']),
        ]);
    }

    public function execute(Request $request, DataSource $dataSource)
    {
        $this->authorize('view', $dataSource);

        $validated = $request->validate([
            'sql' => 'required|string',
            'limit' => 'integer|min:1|max:10000',
        ]);

        // Validate the query
        $validation = $this->queryService->validateQuery($validated['sql']);
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'error' => $validation['error'],
            ], 400);
        }

        // Execute the query
        $result = $this->queryService->executeQuery(
            $dataSource,
            $validated['sql'],
            $validated['limit'] ?? 1000
        );

        // Save successful queries to history
        if ($result['success']) {
            $this->queryService->saveQuery([
                'user_id' => auth()->id(),
                'data_source_id' => $dataSource->id,
                'sql' => $validated['sql'],
                'result_metadata' => [
                    'columns' => $result['columns'],
                ],
                'execution_time' => $result['execution_time'],
                'row_count' => $result['row_count'],
            ]);
        }

        return response()->json($result);
    }

    public function save(Request $request, DataSource $dataSource)
    {
        $this->authorize('view', $dataSource);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sql' => 'required|string',
        ]);

        $query = $this->queryService->saveQuery([
            'user_id' => auth()->id(),
            'data_source_id' => $dataSource->id,
            'name' => $validated['name'],
            'sql' => $validated['sql'],
        ]);

        return response()->json([
            'success' => true,
            'query' => $query,
        ]);
    }

    public function destroy(Query $query)
    {
        $this->authorize('delete', $query);

        $query->delete();

        return redirect()->back()->with('success', 'Query deleted successfully');
    }
}
