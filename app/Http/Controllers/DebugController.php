<?php

namespace App\Http\Controllers;

use App\Models\DataSource;
use App\Services\AI\SchemaAnalyzer;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function checkSchema()
    {
        $dataSource = DataSource::find(1);
        
        if (!$dataSource) {
            return response()->json(['error' => 'Data source not found']);
        }
        
        try {
            $analyzer = app(SchemaAnalyzer::class);
            $schema = $analyzer->getSchemaContext($dataSource);
            
            return response()->json([
                'data_source' => [
                    'id' => $dataSource->id,
                    'name' => $dataSource->name,
                    'type' => $dataSource->type,
                    'status' => $dataSource->status,
                ],
                'schema' => $schema,
                'table_count' => count($schema),
                'tables' => array_keys($schema),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}