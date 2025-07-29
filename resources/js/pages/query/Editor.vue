<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs as TabsRoot, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { basicSetup } from 'codemirror';
import { sql, SQLConfig } from '@codemirror/lang-sql';
import { oneDark } from '@codemirror/theme-one-dark';
import { EditorView } from '@codemirror/view';
import { autocompletion, CompletionContext, CompletionResult } from '@codemirror/autocomplete';
import { Codemirror } from 'vue-codemirror';
import { Database, Download, Play, Save, Clock, AlertCircle } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import axios from 'axios';

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

// Build SQL schema for CodeMirror
const buildSQLSchema = () => {
    if (!props.schema || !props.schema.tables) return {};
    
    const schema: Record<string, string[]> = {};
    
    props.schema.tables.forEach(table => {
        schema[table.name] = table.columns.map(col => col.name);
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
        schema[table].forEach(column => {
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
        'SELECT', 'FROM', 'WHERE', 'GROUP BY', 'ORDER BY', 'HAVING',
        'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'OUTER JOIN',
        'ON', 'AND', 'OR', 'NOT', 'IN', 'EXISTS', 'BETWEEN', 'LIKE',
        'AS', 'DISTINCT', 'COUNT', 'SUM', 'AVG', 'MIN', 'MAX',
        'LIMIT', 'OFFSET', 'UNION', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END'
    ];
    
    keywords.forEach(keyword => {
        options.push({
            label: keyword,
            type: 'keyword',
            apply: keyword,
        });
    });
    
    return {
        from: word.from,
        options: options.filter(opt => 
            opt.label.toLowerCase().startsWith(word.text.toLowerCase())
        ),
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
            limit: 1000,
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
        ...result.value.data.map(row => 
            headers.map(header => {
                const value = row[header];
                // Escape quotes and wrap in quotes if contains comma or newline
                if (typeof value === 'string' && (value.includes(',') || value.includes('\n') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value;
            }).join(',')
        )
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
</script>

<template>
    <Head :title="`Query Editor - ${dataSource.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Query Editor</h1>
                    <p class="text-muted-foreground">
                        Execute SQL queries on {{ dataSource.name }}
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        @click="saveQuery"
                        :disabled="saving || !sqlQuery.trim()"
                    >
                        <Save class="mr-2 h-4 w-4" />
                        {{ saving ? 'Saving...' : 'Save Query' }}
                    </Button>
                    <Button
                        @click="executeQuery"
                        :disabled="executing || !sqlQuery.trim()"
                    >
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
                        <CardDescription>
                            Click to load a saved query
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div v-if="recentQueries.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                            No saved queries yet
                        </div>
                        <div v-else class="divide-y">
                            <button
                                v-for="q in recentQueries"
                                :key="q.id"
                                @click="loadQuery(q)"
                                class="w-full px-4 py-3 text-left hover:bg-muted/50 transition-colors"
                            >
                                <div class="font-medium text-sm">{{ q.name }}</div>
                                <div class="text-xs text-muted-foreground flex items-center gap-1 mt-1">
                                    <Clock class="h-3 w-3" />
                                    {{ new Date(q.created_at).toLocaleDateString() }}
                                </div>
                            </button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Right Panel - Editor and Results -->
                <div class="lg:col-span-3 space-y-4">
                    <!-- Query Name -->
                    <Card v-if="queryName || query">
                        <CardContent class="pt-6">
                            <div class="space-y-2">
                                <Label for="query-name">Query Name</Label>
                                <Input
                                    id="query-name"
                                    v-model="queryName"
                                    placeholder="Enter a name for this query"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- SQL Editor -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">SQL Query</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Codemirror
                                v-model="sqlQuery"
                                :extensions="extensions"
                                :autofocus="true"
                                :indent-with-tab="true"
                            />
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
                        </CardHeader>
                        <CardContent>
                            <!-- Error State -->
                            <div v-if="!result.success" class="rounded-lg border border-red-200 bg-red-50 p-4">
                                <div class="flex gap-3">
                                    <AlertCircle class="h-5 w-5 text-red-600 flex-shrink-0" />
                                    <div class="text-sm text-red-800">
                                        {{ result.error }}
                                    </div>
                                </div>
                            </div>

                            <!-- Results Table -->
                            <div v-else-if="result.data && result.data.length > 0" class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="border-b">
                                            <th
                                                v-for="column in result.columns"
                                                :key="column.name"
                                                class="px-4 py-2 text-left text-sm font-medium"
                                            >
                                                {{ column.name }}
                                                <span class="text-xs text-muted-foreground ml-1">
                                                    ({{ column.type }})
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(row, index) in result.data"
                                            :key="index"
                                            class="border-b hover:bg-muted/50"
                                        >
                                            <td
                                                v-for="column in result.columns"
                                                :key="column.name"
                                                class="px-4 py-2 text-sm"
                                            >
                                                {{ row[column.name] }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-if="result.limited" class="mt-4 text-center text-sm text-muted-foreground">
                                    Results limited to 1,000 rows
                                </div>
                            </div>

                            <!-- Empty Results -->
                            <div v-else class="text-center py-8 text-muted-foreground">
                                Query returned no results
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>