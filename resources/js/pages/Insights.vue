<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, LineChart, PieChart, ScatterChart } from '@/components/charts';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Brain, Lightbulb, Loader2, Send, Sparkles, TrendingUp } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

interface DataSource {
    id: number;
    name: string;
    type: string;
}

interface QueryResult {
    query: {
        sql: string;
        explanation: string;
    };
    result: {
        data: any[];
        columns: { name: string; type: string }[];
        row_count: number;
        execution_time: number;
    };
    visualization: {
        recommendation: {
            type: string;
            reason: string;
            config: any;
        };
        alternatives: Array<{ type: string; reason: string }>;
    };
    explanation: string;
    follow_up_questions: string[];
}

interface ProactiveInsight {
    type: string;
    title: string;
    description: string;
    query: string;
    priority: number;
}

interface Props {
    dataSources: DataSource[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'AI Insights',
        href: '/insights',
    },
];

const query = ref('');
const selectedDataSource = ref<number | null>(props.dataSources[0]?.id || null);
const isLoading = ref(false);
const error = ref<string | null>(null);
const queryResult = ref<QueryResult | null>(null);
const proactiveInsights = ref<ProactiveInsight[]>([]);
const loadingInsights = ref(false);
const activeVisualizationType = ref<string>('table');

const sampleQuestions = [
    'What were our top performing products last month?',
    'Show me revenue trends by region',
    'Which customers have the highest lifetime value?',
    "What's the average order value this quarter?",
];

const selectedVisualizationType = computed(() => {
    // Use active visualization type if user has selected one, otherwise use recommendation
    return activeVisualizationType.value || queryResult.value?.visualization.recommendation.type || 'table';
});

const availableVisualizationTypes = computed(() => {
    // Define all available visualization types
    const allTypes = [
        { type: 'line', label: 'Line' },
        { type: 'bar', label: 'Bar' },
        { type: 'area', label: 'Area' },
        { type: 'pie', label: 'Pie' },
        { type: 'scatter', label: 'Scatter' },
        { type: 'table', label: 'Table' },
    ];
    
    // Filter based on data characteristics if needed
    if (!queryResult.value) return allTypes;
    
    const data = queryResult.value.result.data;
    const columns = queryResult.value.result.columns;
    
    // All types are generally available, but you could add logic here
    // to filter out inappropriate chart types based on data
    return allTypes;
});

const chartData = computed(() => {
    if (!queryResult.value?.result?.data) return [];
    
    const data = queryResult.value.result.data;
    const config = queryResult.value.visualization.recommendation.config;
    
    console.log('Chart type:', selectedVisualizationType.value);
    console.log('Config:', config);
    console.log('Raw data:', data);
    
    // Transform data for bar, line, or area chart (they use the same format)
    if (['bar', 'line', 'area'].includes(selectedVisualizationType.value) && config?.xAxis && config?.yAxis) {
        const transformed = data.map((item: any) => ({
            name: String(item[config.xAxis] || ''),
            value: Number(item[config.yAxis] || 0)
        }));
        console.log('Transformed data for chart:', transformed);
        return transformed;
    }
    
    // Transform data for pie chart
    if (selectedVisualizationType.value === 'pie' && config?.dimension && config?.metric) {
        return data.map((item: any) => ({
            name: String(item[config.dimension] || ''),
            value: Number(item[config.metric] || 0)
        }));
    }
    
    return data;
});

const handleSubmit = async () => {
    if (!query.value.trim()) {
        error.value = 'Please enter a question';
        return;
    }
    
    if (!selectedDataSource.value) {
        error.value = 'Please select a data source';
        return;
    }
    
    isLoading.value = true;
    error.value = null;
    queryResult.value = null;
    
    try {
        console.log('Sending request:', {
            question: query.value,
            data_source_id: selectedDataSource.value,
        });
        
        const response = await axios.post('/insights/ask', {
            question: query.value,
            data_source_id: selectedDataSource.value,
        });
        
        if (response.data.success) {
            queryResult.value = response.data;
            // Reset to recommended visualization when new query is run
            activeVisualizationType.value = response.data.visualization.recommendation.type;
            console.log('Query result:', queryResult.value);
            console.log('Result data:', queryResult.value.result.data);
            console.log('Visualization config:', queryResult.value.visualization);
        } else {
            error.value = response.data.error || 'Failed to process your question';
        }
    } catch (err: any) {
        console.error('API Error:', err);
        console.error('Error response:', err.response);
        console.error('Error response data:', err.response?.data);
        console.error('Error response status:', err.response?.status);
        
        if (err.response?.status === 422 && err.response?.data?.errors) {
            // Validation errors
            const errors = err.response.data.errors;
            error.value = Object.values(errors).flat().join(', ');
        } else if (err.response?.status === 500) {
            // Server error - show more details
            error.value = err.response?.data?.error || err.response?.data?.message || 'Server error occurred. Check the logs for details.';
            console.error('Server error details:', err.response?.data);
        } else {
            error.value = err.response?.data?.error || err.response?.data?.message || 'An error occurred while processing your question';
        }
    } finally {
        isLoading.value = false;
    }
};

const loadProactiveInsights = async () => {
    if (!selectedDataSource.value) return;
    
    loadingInsights.value = true;
    
    try {
        console.log('Loading proactive insights for data source:', selectedDataSource.value);
        const response = await axios.get('/insights/proactive', {
            params: { data_source_id: selectedDataSource.value },
        });
        
        console.log('Proactive insights response:', response.data);
        
        if (response.data.success) {
            proactiveInsights.value = response.data.insights;
        }
    } catch (err: any) {
        console.error('Failed to load proactive insights:', err);
        console.error('Error response:', err.response);
        console.error('Error response data:', err.response?.data);
        console.error('Error response status:', err.response?.status);
        console.error('Error response headers:', err.response?.headers);
        
        // Show user-friendly error message
        error.value = err.response?.data?.error || err.response?.data?.message || 'Failed to load AI insights. Please try again.';
    } finally {
        loadingInsights.value = false;
    }
};

const askFollowUpQuestion = (question: string) => {
    query.value = question;
    handleSubmit();
};

const formatCellValue = (value: any, type?: string): string => {
    if (value === null || value === undefined) return '-';
    
    // Format numbers with commas
    if (typeof value === 'number' || (!isNaN(value) && !isNaN(parseFloat(value)))) {
        return new Intl.NumberFormat().format(Number(value));
    }
    
    // Format dates
    if (type?.includes('date') || type?.includes('time')) {
        try {
            return new Date(value).toLocaleDateString();
        } catch {
            return String(value);
        }
    }
    
    return String(value);
};

onMounted(() => {
    console.log('Data sources:', props.dataSources);
    console.log('Selected data source:', selectedDataSource.value);
    if (selectedDataSource.value) {
        loadProactiveInsights();
    }
});
</script>

<template>
    <Head title="AI Insights" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="text-center">
                <div class="mb-4 inline-flex items-center justify-center rounded-full bg-primary/10 p-3">
                    <Sparkles class="h-8 w-8 text-primary" />
                </div>
                <h1 class="text-3xl font-bold tracking-tight">AI-Powered Insights</h1>
                <p class="mx-auto mt-2 max-w-2xl text-muted-foreground">
                    Ask questions about your data in natural language and get instant visualizations and answers
                </p>
            </div>

            <!-- Query Input -->
            <form @submit.prevent="handleSubmit" class="mx-auto w-full max-w-3xl">
                <div class="relative">
                    <Input v-model="query" placeholder="Ask a question about your data..." class="h-12 pr-12 text-base" />
                    <Button type="submit" size="icon" class="absolute top-1 right-1 h-10 w-10" :disabled="!query.trim()">
                        <Send class="h-4 w-4" />
                    </Button>
                </div>
            </form>

            <!-- Sample Questions -->
            <div class="mx-auto w-full max-w-3xl">
                <p class="mb-3 text-sm text-muted-foreground">Try asking:</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <Button
                        v-for="question in sampleQuestions"
                        :key="question"
                        variant="outline"
                        class="h-auto justify-start px-4 py-3 text-left"
                        @click="query = question"
                    >
                        <ArrowRight class="mr-2 h-4 w-4 shrink-0" />
                        <span class="text-sm">{{ question }}</span>
                    </Button>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <Brain class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Natural Language</CardTitle>
                        <CardDescription> Ask questions the way you think, no SQL required </CardDescription>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <TrendingUp class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Smart Visualizations</CardTitle>
                        <CardDescription> Automatically generates the best charts for your data </CardDescription>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <Lightbulb class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Proactive Insights</CardTitle>
                        <CardDescription> Discover trends and anomalies you didn't know existed </CardDescription>
                    </CardHeader>
                </Card>
            </div>

            <!-- Data Source Selection -->
            <div class="mx-auto w-full max-w-3xl mb-4">
                <Select v-model="selectedDataSource" @update:model-value="loadProactiveInsights">
                    <SelectTrigger>
                        <SelectValue placeholder="Select a data source" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="ds in dataSources" :key="ds.id" :value="ds.id">
                            {{ ds.name }} ({{ ds.type }})
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            
            <!-- Error Alert -->
            <Alert v-if="error" variant="destructive" class="mx-auto w-full max-w-3xl mb-4">
                <AlertCircle class="h-4 w-4" />
                <AlertDescription>{{ error }}</AlertDescription>
            </Alert>
            
            <!-- Loading State -->
            <Card v-if="isLoading" class="mt-4">
                <CardContent class="p-6">
                    <div class="flex items-center justify-center space-x-2">
                        <Loader2 class="h-6 w-6 animate-spin" />
                        <p>Analyzing your question...</p>
                    </div>
                </CardContent>
            </Card>
            
            <!-- Query Results -->
            <div v-else-if="queryResult" class="mt-4 space-y-4">
                <!-- Query Explanation -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Query Explanation</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground mb-2">{{ queryResult.query.explanation }}</p>
                        <details class="mt-2">
                            <summary class="cursor-pointer text-sm font-medium">View SQL Query</summary>
                            <pre class="mt-2 p-3 bg-muted rounded text-xs overflow-x-auto">{{ queryResult.query.sql }}</pre>
                        </details>
                    </CardContent>
                </Card>
                
                <!-- Results Summary -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Results</CardTitle>
                        <CardDescription>
                            {{ queryResult.explanation }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 text-sm text-muted-foreground">
                            <span>{{ queryResult.result.row_count }} rows</span>
                            <span>•</span>
                            <span>{{ queryResult.result.execution_time }}ms</span>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Visualization -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Visualization</CardTitle>
                        <CardDescription>
                            {{ queryResult.visualization.recommendation.reason }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="selectedVisualizationType === 'bar' && chartData.length > 0" class="h-96">
                            <BarChart
                                :data="chartData"
                                :x-axis-label="queryResult.visualization.recommendation.config.xAxis"
                                :y-axis-label="queryResult.visualization.recommendation.config.yAxis"
                            />
                        </div>
                        <div v-else-if="selectedVisualizationType === 'line' && chartData.length > 0" class="h-96">
                            <LineChart
                                :data="chartData"
                                :x-axis-label="queryResult.visualization.recommendation.config.xAxis"
                                :y-axis-label="queryResult.visualization.recommendation.config.yAxis"
                            />
                        </div>
                        <div v-else-if="selectedVisualizationType === 'area' && chartData.length > 0" class="h-96">
                            <LineChart
                                :data="chartData"
                                :x-axis-label="queryResult.visualization.recommendation.config.xAxis"
                                :y-axis-label="queryResult.visualization.recommendation.config.yAxis"
                                :area="true"
                            />
                        </div>
                        <div v-else-if="selectedVisualizationType === 'pie' && chartData.length > 0" class="h-96">
                            <PieChart
                                :data="chartData"
                            />
                        </div>
                        <div v-else-if="selectedVisualizationType === 'scatter' && queryResult.visualization.recommendation.config" class="h-96">
                            <ScatterChart
                                :data="queryResult.result.data"
                                :x-key="queryResult.visualization.recommendation.config.xAxis"
                                :y-key="queryResult.visualization.recommendation.config.yAxis"
                            />
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="border-b bg-muted/50">
                                        <th v-for="col in queryResult.result.columns" :key="col.name" class="text-left p-3 font-medium">
                                            {{ col.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in queryResult.result.data" :key="idx" class="border-b hover:bg-muted/30">
                                        <td v-for="col in queryResult.result.columns" :key="col.name" class="p-3">
                                            {{ formatCellValue(row[col.name], col.type) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p v-if="queryResult.result.row_count > queryResult.result.data.length" class="text-sm text-muted-foreground mt-2">
                                Showing {{ queryResult.result.data.length }} of {{ queryResult.result.row_count }} rows
                            </p>
                        </div>
                        
                        <!-- Alternative Visualizations -->
                        <div class="mt-4">
                            <p class="text-sm font-medium mb-2">Try other visualizations:</p>
                            <div class="flex gap-2 flex-wrap">
                                <Button
                                    v-for="vizType in availableVisualizationTypes"
                                    :key="vizType.type"
                                    size="sm"
                                    :variant="activeVisualizationType === vizType.type ? 'default' : 'outline'"
                                    @click="activeVisualizationType = vizType.type"
                                >
                                    {{ vizType.label }}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Follow-up Questions -->
                <Card v-if="queryResult.follow_up_questions.length > 0">
                    <CardHeader>
                        <CardTitle class="text-lg">Follow-up Questions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <Button
                                v-for="(question, idx) in queryResult.follow_up_questions"
                                :key="idx"
                                variant="outline"
                                class="w-full justify-start"
                                @click="askFollowUpQuestion(question)"
                            >
                                <ArrowRight class="mr-2 h-4 w-4" />
                                {{ question }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Proactive Insights -->
            <div v-else-if="!query && (proactiveInsights.length > 0 || loadingInsights)" class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Discover Insights</h2>
                
                <!-- Loading state for proactive insights -->
                <div v-if="loadingInsights" class="grid gap-4 md:grid-cols-2">
                    <Card v-for="i in 4" :key="i" class="animate-pulse">
                        <CardHeader>
                            <div class="h-5 bg-muted rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-muted rounded w-full"></div>
                        </CardHeader>
                    </Card>
                </div>
                
                <!-- Actual insights -->
                <div v-else class="grid gap-4 md:grid-cols-2">
                    <Card
                        v-for="insight in proactiveInsights"
                        :key="insight.title"
                        class="cursor-pointer hover:shadow-lg transition-shadow"
                        @click="askFollowUpQuestion(insight.query)"
                    >
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <CardTitle class="text-base">{{ insight.title }}</CardTitle>
                                <Sparkles class="h-4 w-4 text-primary" />
                            </div>
                            <CardDescription>
                                {{ insight.description }}
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
            
            <!-- Empty State -->
            <Card v-else class="mt-4 flex-1">
                <CardContent class="flex h-full min-h-[300px] items-center justify-center text-muted-foreground">
                    <div class="text-center">
                        <Sparkles class="mx-auto mb-4 h-12 w-12 opacity-20" />
                        <p>Your insights will appear here</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
