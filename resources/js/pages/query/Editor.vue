<script setup lang="ts">
import { BarChart, LineChart, PieChart, ScatterChart } from '@/components/charts';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { TabsContent, TabsList, Tabs as TabsRoot, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { autocompletion, CompletionContext, CompletionResult } from '@codemirror/autocomplete';
import { sql, SQLConfig } from '@codemirror/lang-sql';
import { oneDark } from '@codemirror/theme-one-dark';
import { EditorView } from '@codemirror/view';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { basicSetup } from 'codemirror';
import { AlertCircle, BarChart2, Clock, Download, Play, Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Codemirror } from 'vue-codemirror';

interface DataSource {
    id: number;
    name: string;
    type: string;
}

interface Query {
    id: number;
    name: string;
    sql: string;
    created_at: string;
}

interface QueryResult {
    success: boolean;
    data?: any[];
    columns?: Array<{ name: string; type: string }>;
    row_count?: number;
    execution_time?: number;
    limited?: boolean;
    error?: string;
}

interface Schema {
    tables: Array<{
        name: string;
        columns: Array<{
            name: string;
            type: string;
            nullable: boolean;
            default: any;
            primary: boolean;
        }>;
    }>;
}

interface Props {
    dataSource: DataSource;
    query?: Query;
    recentQueries: Query[];
    schema: Schema | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Data Sources',
        href: '/data-sources',
    },
    {
        title: props.dataSource.name,
        href: route('data-sources.show', props.dataSource.id),
    },
    {
        title: 'Query Editor',
        href: route('query.editor', props.dataSource.id),
    },
];

// Editor state
const sqlQuery = ref(props.query?.sql || 'SELECT * FROM ');
const queryName = ref(props.query?.name || '');
const executing = ref(false);
const saving = ref(false);
const result = ref<QueryResult | null>(null);
const activeTab = ref('results');
const queryLimit = ref(1000);
const showVisualization = ref(false);
const selectedChartType = ref<'bar' | 'line' | 'pie' | 'scatter'>('bar');
const chartConfig = ref({
    xAxis: '',
    yAxis: '',
    series: [] as string[],
});

// Build SQL schema for CodeMirror
const buildSQLSchema = () => {
    if (!props.schema || !props.schema.tables) return {};

    const schema: Record<string, string[]> = {};

    props.schema.tables.forEach((table) => {
        schema[table.name] = table.columns.map((col) => col.name);
    });

    return schema;
};

// Custom SQL completion
const sqlCompletion = (context: CompletionContext): CompletionResult | null => {
    const word = context.matchBefore(/\w*/);
    if (!word || (word.from === word.to && !context.explicit)) return null;

    const schema = buildSQLSchema();
    const options = [];

    // Add table names
    for (const table in schema) {
        options.push({
            label: table,
            type: 'type',
            apply: table,
            detail: 'table',
        });
    }

    // Add column names for all tables
    for (const table in schema) {
        schema[table].forEach((column) => {
            options.push({
                label: `${table}.${column}`,
                type: 'property',
                apply: `${table}.${column}`,
                detail: 'column',
            });
            // Also add just the column name
            options.push({
                label: column,
                type: 'property',
                apply: column,
                detail: `column in ${table}`,
            });
        });
    }

    // Add SQL keywords
    const keywords = [
        'SELECT',
        'FROM',
        'WHERE',
        'GROUP BY',
        'ORDER BY',
        'HAVING',
        'JOIN',
        'LEFT JOIN',
        'RIGHT JOIN',
        'INNER JOIN',
        'OUTER JOIN',
        'ON',
        'AND',
        'OR',
        'NOT',
        'IN',
        'EXISTS',
        'BETWEEN',
        'LIKE',
        'AS',
        'DISTINCT',
        'COUNT',
        'SUM',
        'AVG',
        'MIN',
        'MAX',
        'LIMIT',
        'OFFSET',
        'UNION',
        'CASE',
        'WHEN',
        'THEN',
        'ELSE',
        'END',
    ];

    keywords.forEach((keyword) => {
        options.push({
            label: keyword,
            type: 'keyword',
            apply: keyword,
        });
    });

    return {
        from: word.from,
        options: options.filter((opt) => opt.label.toLowerCase().startsWith(word.text.toLowerCase())),
    };
};

// CodeMirror extensions
const extensions = computed(() => {
    const isDark = document.documentElement.classList.contains('dark');

    // SQL configuration with schema
    const sqlConfig: SQLConfig = {
        schema: buildSQLSchema(),
        upperCaseKeywords: true,
    };

    return [
        basicSetup,
        sql(sqlConfig),
        isDark ? oneDark : [],
        autocompletion({
            override: [sqlCompletion],
        }),
        EditorView.theme({
            '&': { height: '300px' },
            '.cm-content': { padding: '12px' },
            '.cm-focused .cm-cursor': { borderLeftColor: '#528bff' },
        }),
    ];
});

// Execute query
const executeQuery = async () => {
    executing.value = true;
    result.value = null;
    activeTab.value = 'results';

    try {
        const response = await axios.post(route('query.execute', props.dataSource.id), {
            sql: sqlQuery.value,
            limit: queryLimit.value,
        });

        result.value = response.data;
    } catch (error: any) {
        result.value = {
            success: false,
            error: error.response?.data?.error || 'Failed to execute query',
        };
    } finally {
        executing.value = false;
    }
};

// Save query
const saveQuery = async () => {
    if (!queryName.value.trim()) {
        alert('Please enter a name for the query');
        return;
    }

    saving.value = true;

    try {
        await axios.post(route('query.save', props.dataSource.id), {
            name: queryName.value,
            sql: sqlQuery.value,
        });

        router.reload({ only: ['recentQueries'] });
    } catch (error) {
        alert('Failed to save query');
    } finally {
        saving.value = false;
    }
};

// Load saved query
const loadQuery = (query: Query) => {
    sqlQuery.value = query.sql;
    queryName.value = query.name;
    result.value = null;
};

// Export results as CSV
const exportResults = () => {
    if (!result.value?.data || result.value.data.length === 0) return;

    const headers = Object.keys(result.value.data[0]);
    const csv = [
        headers.join(','),
        ...result.value.data.map((row) =>
            headers
                .map((header) => {
                    const value = row[header];
                    // Escape quotes and wrap in quotes if contains comma or newline
                    if (typeof value === 'string' && (value.includes(',') || value.includes('\n') || value.includes('"'))) {
                        return `"${value.replace(/"/g, '""')}"`;
                    }
                    return value;
                })
                .join(','),
        ),
    ].join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `query-results-${new Date().toISOString()}.csv`;
    a.click();
    URL.revokeObjectURL(url);
};

// Format execution time
const formatExecutionTime = (ms: number) => {
    if (ms < 1000) return `${ms}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
};

// Analyze data types
const analyzeDataTypes = (data: any[], columns: Array<{ name: string; type: string }>) => {
    const analysis: Record<string, { type: string; isNumeric: boolean; isDate: boolean; uniqueCount: number }> = {};

    columns.forEach((col) => {
        const values = data.map((row) => row[col.name]).filter((v) => v !== null && v !== undefined);
        const uniqueValues = new Set(values);

        // Check if numeric
        const isNumeric = values.every((v) => !isNaN(Number(v)));

        // Check if date
        const isDate = values.every((v) => !isNaN(Date.parse(String(v))));

        analysis[col.name] = {
            type: col.type,
            isNumeric,
            isDate,
            uniqueCount: uniqueValues.size,
        };
    });

    return analysis;
};

// Suggest chart types based on data
const suggestChartTypes = () => {
    if (!result.value?.data || !result.value?.columns) return [];

    const analysis = analyzeDataTypes(result.value.data, result.value.columns);
    const numericColumns = Object.keys(analysis).filter((col) => analysis[col].isNumeric);
    const categoricalColumns = Object.keys(analysis).filter((col) => !analysis[col].isNumeric);

    const suggestions = [];

    // Bar chart: good for categorical + numeric
    if (categoricalColumns.length > 0 && numericColumns.length > 0) {
        suggestions.push('bar');
    }

    // Line chart: good for time series or continuous numeric
    if (numericColumns.length >= 2 || (categoricalColumns.some((col) => analysis[col].isDate) && numericColumns.length > 0)) {
        suggestions.push('line');
    }

    // Pie chart: good for categorical + single numeric with limited categories
    if (categoricalColumns.length > 0 && numericColumns.length > 0 && categoricalColumns.some((col) => analysis[col].uniqueCount <= 10)) {
        suggestions.push('pie');
    }

    // Scatter: good for two numeric columns
    if (numericColumns.length >= 2) {
        suggestions.push('scatter');
    }

    return suggestions;
};

// Initialize visualization
const initializeVisualization = () => {
    if (!result.value?.data || !result.value?.columns) return;

    showVisualization.value = true;
    activeTab.value = 'visualization';

    const analysis = analyzeDataTypes(result.value.data, result.value.columns);
    const numericColumns = Object.keys(analysis).filter((col) => analysis[col].isNumeric);
    const categoricalColumns = Object.keys(analysis).filter((col) => !analysis[col].isNumeric);

    // Auto-select chart type
    const suggestions = suggestChartTypes();
    if (suggestions.length > 0) {
        selectedChartType.value = suggestions[0] as any;
    }

    // Auto-configure axes
    if (selectedChartType.value === 'bar' || selectedChartType.value === 'line') {
        chartConfig.value.xAxis = categoricalColumns[0] || numericColumns[0] || '';
        chartConfig.value.yAxis = numericColumns[0] || '';
        chartConfig.value.series = numericColumns.slice(0, 3);
    } else if (selectedChartType.value === 'pie') {
        chartConfig.value.xAxis = categoricalColumns[0] || '';
        chartConfig.value.yAxis = numericColumns[0] || '';
    } else if (selectedChartType.value === 'scatter') {
        chartConfig.value.xAxis = numericColumns[0] || '';
        chartConfig.value.yAxis = numericColumns[1] || '';
    }
};

// Prepare chart data
const chartData = computed(() => {
    if (!result.value?.data || !chartConfig.value.xAxis) return null;

    if (selectedChartType.value === 'pie') {
        // Aggregate data for pie chart
        const aggregated: Record<string, number> = {};
        result.value.data.forEach((row) => {
            const key = String(row[chartConfig.value.xAxis]);
            const value = Number(row[chartConfig.value.yAxis]) || 0;
            aggregated[key] = (aggregated[key] || 0) + value;
        });

        return Object.entries(aggregated).map(([name, value]) => ({ name, value }));
    } else if (selectedChartType.value === 'scatter') {
        return result.value.data.map((row) => ({
            x: Number(row[chartConfig.value.xAxis]) || 0,
            y: Number(row[chartConfig.value.yAxis]) || 0,
            name: row[chartConfig.value.xAxis],
        }));
    } else {
        // Bar and Line charts
        const xValues = [...new Set(result.value.data.map((row) => String(row[chartConfig.value.xAxis])))];

        if (chartConfig.value.series.length > 0) {
            // Multiple series
            const series = chartConfig.value.series.map((seriesName) => ({
                name: seriesName,
                data: xValues.map((x) => {
                    const row = result.value!.data.find((r) => String(r[chartConfig.value.xAxis]) === x);
                    return row ? Number(row[seriesName]) || 0 : 0;
                }),
            }));

            return {
                categories: xValues,
                series,
            };
        } else {
            // Single series
            return {
                categories: xValues,
                series: [
                    {
                        name: chartConfig.value.yAxis,
                        data: xValues.map((x) => {
                            const row = result.value!.data.find((r) => String(r[chartConfig.value.xAxis]) === x);
                            return row ? Number(row[chartConfig.value.yAxis]) || 0 : 0;
                        }),
                    },
                ],
            };
        }
    }
});

// Available columns for chart configuration
const availableColumns = computed(() => {
    if (!result.value?.columns) return { all: [], numeric: [], categorical: [] };

    const analysis = analyzeDataTypes(result.value.data || [], result.value.columns);

    return {
        all: result.value.columns.map((col) => col.name),
        numeric: Object.keys(analysis).filter((col) => analysis[col].isNumeric),
        categorical: Object.keys(analysis).filter((col) => !analysis[col].isNumeric),
    };
});
</script>

<template>
    <Head :title="`Query Editor - ${dataSource.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Query Editor</h1>
                    <p class="text-muted-foreground">Execute SQL queries on {{ dataSource.name }}</p>
                </div>

                <div class="flex gap-2">
                    <Button variant="outline" @click="saveQuery" :disabled="saving || !sqlQuery.trim()">
                        <Save class="mr-2 h-4 w-4" />
                        {{ saving ? 'Saving...' : 'Save Query' }}
                    </Button>
                    <Button @click="executeQuery" :disabled="executing || !sqlQuery.trim()">
                        <Play class="mr-2 h-4 w-4" />
                        {{ executing ? 'Executing...' : 'Run Query' }}
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-4">
                <!-- Left Panel - Recent Queries -->
                <Card class="lg:col-span-1">
                    <CardHeader>
                        <CardTitle class="text-base">Recent Queries</CardTitle>
                        <CardDescription> Click to load a saved query </CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div v-if="recentQueries.length === 0" class="p-4 text-center text-sm text-muted-foreground">No saved queries yet</div>
                        <div v-else class="divide-y">
                            <button
                                v-for="q in recentQueries"
                                :key="q.id"
                                @click="loadQuery(q)"
                                class="w-full px-4 py-3 text-left transition-colors hover:bg-muted/50"
                                :class="{ 'opacity-60': !q.name }"
                            >
                                <div class="text-sm font-medium">{{ q.name || 'Unnamed Query' }}</div>
                                <div class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                    <Clock class="h-3 w-3" />
                                    {{ new Date(q.created_at).toLocaleDateString() }}
                                </div>
                            </button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Right Panel - Editor and Results -->
                <div class="space-y-4 lg:col-span-3">
                    <!-- Query Name -->
                    <Card>
                        <CardContent class="pt-6">
                            <div class="space-y-2">
                                <Label for="query-name">Query Name</Label>
                                <Input id="query-name" v-model="queryName" placeholder="Enter a name for this query" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- SQL Editor -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-base">SQL Query</CardTitle>
                                <div class="flex items-center gap-2">
                                    <Label for="limit" class="text-sm">Limit:</Label>
                                    <Select :model-value="String(queryLimit)" @update:model-value="(value) => (queryLimit = parseInt(value))">
                                        <SelectTrigger id="limit" class="w-[120px]">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="100">100 rows</SelectItem>
                                            <SelectItem value="500">500 rows</SelectItem>
                                            <SelectItem value="1000">1,000 rows</SelectItem>
                                            <SelectItem value="5000">5,000 rows</SelectItem>
                                            <SelectItem value="10000">10,000 rows</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <Codemirror v-model="sqlQuery" :extensions="extensions" :autofocus="true" :indent-with-tab="true" />
                        </CardContent>
                    </Card>

                    <!-- Results -->
                    <Card v-if="result">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-base">
                                    {{ result.success ? 'Query Results' : 'Query Error' }}
                                </CardTitle>
                                <div v-if="result.success" class="flex items-center gap-4 text-sm text-muted-foreground">
                                    <span>{{ result.row_count }} rows</span>
                                    <span>{{ formatExecutionTime(result.execution_time || 0) }}</span>
                                    <div class="flex gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="initializeVisualization"
                                            :disabled="!result.data || result.data.length === 0"
                                        >
                                            <BarChart2 class="mr-1 h-4 w-4" />
                                            Visualize
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="exportResults"
                                            :disabled="!result.data || result.data.length === 0"
                                        >
                                            <Download class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <!-- Error State -->
                            <div v-if="!result.success" class="rounded-lg border border-red-200 bg-red-50 p-4">
                                <div class="flex gap-3">
                                    <AlertCircle class="h-5 w-5 flex-shrink-0 text-red-600" />
                                    <div class="text-sm text-red-800">
                                        {{ result.error }}
                                    </div>
                                </div>
                            </div>

                            <!-- Success State with Tabs -->
                            <div v-else-if="result.data">
                                <TabsRoot v-model="activeTab" v-if="showVisualization">
                                    <TabsList class="mb-4">
                                        <TabsTrigger value="results">Results Table</TabsTrigger>
                                        <TabsTrigger value="visualization">Visualization</TabsTrigger>
                                    </TabsList>

                                    <!-- Results Tab -->
                                    <TabsContent value="results">
                                        <div v-if="result.data.length > 0" class="relative">
                                            <div
                                                class="scrollbar-thin scrollbar-thumb-muted scrollbar-track-transparent max-h-[600px] overflow-auto rounded border"
                                            >
                                                <table class="w-full min-w-max border-collapse">
                                                    <thead class="sticky top-0 z-10">
                                                        <tr class="border-b bg-background">
                                                            <th
                                                                v-for="column in result.columns"
                                                                :key="column.name"
                                                                class="border-b px-4 py-2 text-left text-sm font-medium whitespace-nowrap"
                                                            >
                                                                {{ column.name }}
                                                                <span class="ml-1 text-xs text-muted-foreground"> ({{ column.type }}) </span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(row, index) in result.data" :key="index" class="border-b hover:bg-muted/50">
                                                            <td
                                                                v-for="column in result.columns"
                                                                :key="column.name"
                                                                class="px-4 py-2 text-sm whitespace-nowrap"
                                                            >
                                                                {{ row[column.name] }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div v-if="result.limited" class="mt-4 text-center text-sm text-muted-foreground">
                                                Results limited to {{ queryLimit.toLocaleString() }} rows
                                            </div>
                                        </div>
                                        <div v-else class="py-8 text-center text-muted-foreground">Query returned no results</div>
                                    </TabsContent>

                                    <!-- Visualization Tab -->
                                    <TabsContent value="visualization" class="space-y-4">
                                        <!-- Chart Type Selection -->
                                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                                            <div>
                                                <Label>Chart Type</Label>
                                                <Select v-model="selectedChartType">
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="bar">Bar Chart</SelectItem>
                                                        <SelectItem value="line">Line Chart</SelectItem>
                                                        <SelectItem value="pie">Pie Chart</SelectItem>
                                                        <SelectItem value="scatter">Scatter Plot</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>

                                            <!-- X-Axis Selection -->
                                            <div>
                                                <Label>X-Axis</Label>
                                                <Select v-model="chartConfig.xAxis">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select column" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="col in availableColumns.all" :key="col" :value="col">
                                                            {{ col }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>

                                            <!-- Y-Axis Selection -->
                                            <div>
                                                <Label>Y-Axis</Label>
                                                <Select v-model="chartConfig.yAxis">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select column" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="col in availableColumns.numeric" :key="col" :value="col">
                                                            {{ col }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>

                                        <!-- Chart Display -->
                                        <div v-if="chartData" class="mt-6">
                                            <BarChart v-if="selectedChartType === 'bar'" :data="chartData" height="400px" />
                                            <LineChart
                                                v-else-if="selectedChartType === 'line' && chartData.categories"
                                                :data="chartData"
                                                height="400px"
                                            />
                                            <PieChart v-else-if="selectedChartType === 'pie'" :data="chartData" height="400px" />
                                            <ScatterChart v-else-if="selectedChartType === 'scatter'" :data="chartData" height="400px" />
                                        </div>
                                    </TabsContent>
                                </TabsRoot>

                                <!-- No tabs mode - just show table -->
                                <div v-else>
                                    <div v-if="result.data.length > 0" class="relative">
                                        <div
                                            class="scrollbar-thin scrollbar-thumb-muted scrollbar-track-transparent max-h-[600px] overflow-auto rounded border"
                                        >
                                            <table class="w-full min-w-max border-collapse">
                                                <thead class="sticky top-0 z-10">
                                                    <tr class="border-b bg-background">
                                                        <th
                                                            v-for="column in result.columns"
                                                            :key="column.name"
                                                            class="border-b px-4 py-2 text-left text-sm font-medium whitespace-nowrap"
                                                        >
                                                            {{ column.name }}
                                                            <span class="ml-1 text-xs text-muted-foreground"> ({{ column.type }}) </span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(row, index) in result.data" :key="index" class="border-b hover:bg-muted/50">
                                                        <td
                                                            v-for="column in result.columns"
                                                            :key="column.name"
                                                            class="px-4 py-2 text-sm whitespace-nowrap"
                                                        >
                                                            {{ row[column.name] }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div v-if="result.limited" class="mt-4 text-center text-sm text-muted-foreground">
                                            Results limited to {{ queryLimit.toLocaleString() }} rows
                                        </div>
                                    </div>
                                    <div v-else class="py-8 text-center text-muted-foreground">Query returned no results</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom scrollbar styles for better visibility */
.overflow-auto::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.5);
}

/* Dark mode scrollbar */
.dark .overflow-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.dark .overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
}

.dark .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Ensure both scrollbars are always visible when content overflows */
.overflow-auto {
    overflow: auto !important;
}
</style>
