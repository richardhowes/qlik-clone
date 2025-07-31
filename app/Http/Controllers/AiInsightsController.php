<?php

namespace App\Http\Controllers;

use App\Models\DataSource;
use App\Models\Query;
use App\Services\AI\AIQueryService;
use App\Services\AI\InsightsService;
use App\Services\AI\SchemaAnalyzer;
use App\Services\AI\VisualizationService;
use App\Services\Query\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AiInsightsController extends Controller
{
    protected AIQueryService $aiQueryService;

    protected InsightsService $insightsService;

    protected VisualizationService $visualizationService;

    protected QueryService $queryService;

    public function __construct(
        AIQueryService $aiQueryService,
        InsightsService $insightsService,
        VisualizationService $visualizationService,
        QueryService $queryService
    ) {
        $this->aiQueryService = $aiQueryService;
        $this->insightsService = $insightsService;
        $this->visualizationService = $visualizationService;
        $this->queryService = $queryService;
    }

    public function index()
    {
        $dataSources = Auth::user()->dataSources()
            ->where('status', 'active')
            ->get(['id', 'name', 'type']);

        return Inertia::render('Insights', [
            'dataSources' => $dataSources,
        ]);
    }

    public function askQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:3|max:500',
            'data_source_id' => 'required|exists:data_sources,id',
        ]);

        $dataSource = DataSource::findOrFail($request->data_source_id);

        // Ensure user owns the data source
        if ($dataSource->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Translate natural language to SQL
            $translationResult = $this->aiQueryService->translateNaturalLanguageToSQL(
                $request->question,
                $dataSource
            );

            if (! $translationResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $translationResult['error'],
                ], 400);
            }

            // Execute the generated SQL
            $queryResult = $this->queryService->executeQuery(
                $dataSource,
                $translationResult['sql']
            );

            if (! $queryResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Query execution failed: '.$queryResult['error'],
                ], 400);
            }

            // Get visualization recommendation
            $visualization = $this->visualizationService->recommendVisualization(
                $queryResult,
                $request->question
            );

            // Generate explanation of results
            $explanation = $this->insightsService->explainQueryResult(
                $queryResult,
                $request->question
            );

            // Get follow-up questions
            $followUpQuestions = $this->aiQueryService->suggestFollowUpQuestions(
                $request->question,
                $queryResult
            );

            // Save query if results were returned
            if ($queryResult['row_count'] > 0) {
                Query::create([
                    'user_id' => Auth::id(),
                    'data_source_id' => $dataSource->id,
                    'name' => substr($request->question, 0, 100),
                    'sql' => $translationResult['sql'],
                    'result_metadata' => [
                        'question' => $request->question,
                        'explanation' => $translationResult['explanation'],
                    ],
                    'execution_time' => $queryResult['execution_time'],
                    'row_count' => $queryResult['row_count'],
                ]);
            }

            return response()->json([
                'success' => true,
                'query' => [
                    'sql' => $translationResult['sql'],
                    'explanation' => $translationResult['explanation'],
                ],
                'result' => [
                    'data' => $queryResult['data'],
                    'columns' => $queryResult['columns'],
                    'row_count' => $queryResult['row_count'],
                    'execution_time' => $queryResult['execution_time'],
                ],
                'visualization' => $visualization,
                'explanation' => $explanation,
                'follow_up_questions' => $followUpQuestions,
            ]);
        } catch (\Exception $e) {
            Log::error('AI insights error', [
                'question' => $request->question,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your question. Please try again.',
            ], 500);
        }
    }

    public function getProactiveInsights(Request $request)
    {
        $request->validate([
            'data_source_id' => 'required|exists:data_sources,id',
        ]);

        $dataSource = DataSource::findOrFail($request->data_source_id);

        // Ensure user owns the data source
        if ($dataSource->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $insights = $this->insightsService->generateProactiveInsights($dataSource);

            return response()->json($insights);
        } catch (\Exception $e) {
            Log::error('Proactive insights error', [
                'data_source_id' => $request->data_source_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'insights' => [],
                'error' => 'Failed to generate insights: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function generateVisualization(Request $request)
    {
        $request->validate([
            'query_data' => 'required|array',
            'chart_type' => 'required|string|in:bar,line,pie,scatter,table',
        ]);

        try {
            $chartConfig = $this->visualizationService->generateChartConfig(
                $request->query_data,
                $request->chart_type
            );

            return response()->json([
                'success' => true,
                'config' => $chartConfig,
            ]);
        } catch (\Exception $e) {
            Log::error('Visualization generation error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate visualization',
            ], 500);
        }
    }

    public function saveInsight(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'query_id' => 'required|exists:queries,id',
            'visualization_config' => 'required|array',
        ]);

        $query = Query::findOrFail($request->query_id);

        // Ensure user owns the query
        if ($query->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // This would save to a new insights table or dashboard
            // For now, we'll return success
            return response()->json([
                'success' => true,
                'message' => 'Insight saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save insight',
            ], 500);
        }
    }
    
    public function debugDataSource(Request $request)
    {
        $request->validate([
            'data_source_id' => 'required|exists:data_sources,id',
        ]);

        $dataSource = DataSource::findOrFail($request->data_source_id);

        // Ensure user owns the data source
        if ($dataSource->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $schema = app(SchemaAnalyzer::class)->getSchemaContext($dataSource);
            
            return response()->json([
                'success' => true,
                'data_source' => [
                    'id' => $dataSource->id,
                    'name' => $dataSource->name,
                    'type' => $dataSource->type,
                    'status' => $dataSource->status,
                ],
                'schema' => $schema,
                'table_count' => count($schema),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
