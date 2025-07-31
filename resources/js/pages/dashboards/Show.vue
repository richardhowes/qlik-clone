<script setup lang="ts">
import { BarChart, LineChart, PieChart, ScatterChart } from '@/components/charts';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { Pencil, Share2, Star } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Query {
    id: number;
    name: string;
    sql: string;
    data_source?: {
        id: number;
        name: string;
    };
    dataSource?: {
        id: number;
        name: string;
    };
    data_source_id?: number;
}

interface Widget {
    id: number;
    dashboard_id: number;
    query_id: number | null;
    type: 'chart' | 'text' | 'metric' | 'table';
    title: string;
    config: {
        chartType?: 'bar' | 'line' | 'pie' | 'scatter';
        xAxis?: string;
        yAxis?: string;
        series?: string[];
    };
    layout: {
        x: number;
        y: number;
        w: number;
        h: number;
        i: string;
    };
    order: number;
    savedQuery?: Query;
    saved_query?: Query; // Handle snake_case from Laravel
}

interface Dashboard {
    id: number;
    name: string;
    description?: string;
    slug: string;
    is_favorite: boolean;
    widgets: Widget[];
}

interface Props {
    dashboard: Dashboard;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Dashboards',
        href: '/dashboards',
    },
    {
        title: props.dashboard.name,
        href: route('dashboards.show', props.dashboard.id),
    },
];

// Widget data storage
const widgetData = ref<Record<string, any>>({});
const loadingWidgets = ref<Set<string>>(new Set());

// Execute query to get data for widget
const executeQuery = async (widget: Widget) => {
    if (!widget.query_id) return null;

    try {
        // Check if savedQuery is properly loaded (handle both camelCase and snake_case)
        const savedQuery = widget.savedQuery || widget.saved_query;
        if (!savedQuery) {
            console.error('SavedQuery not loaded for widget:', widget);
            return null;
        }

        // Get data source ID (handle both camelCase and snake_case)
        const dataSource = savedQuery.dataSource || savedQuery.data_source;
        const dataSourceId = dataSource?.id || savedQuery.data_source_id;
        if (!dataSourceId) {
            console.error('No data source ID found for query:', savedQuery);
            return null;
        }

        console.log('Executing query for widget:', widget.id, 'DataSource:', dataSourceId);
        console.log('Query SQL:', savedQuery.sql);

        const response = await axios.post(route('query.execute', dataSourceId), {
            sql: savedQuery.sql,
            limit: 1000,
        });

        console.log('Query response for widget', widget.id, ':', response.data);

        // Return the full query result structure (columns + data)
        if (response.data.success) {
            return {
                columns: response.data.columns || [],
                data: response.data.data || [],
                row_count: response.data.row_count,
                execution_time: response.data.execution_time,
            };
        }

        throw new Error('Query execution failed');
    } catch (error) {
        console.error('Failed to execute query:', error);
        return null;
    }
};

// Load widget data
const loadWidgetData = async (widget: Widget) => {
    if (!widget.query_id || !widget.id) return;

    const widgetKey = String(widget.id);
    loadingWidgets.value.add(widgetKey);

    try {
        const data = await executeQuery(widget);
        console.log('Widget data received:', data);
        console.log('Widget key:', widgetKey);
        if (data) {
            widgetData.value[widgetKey] = data;
            console.log('Widget data stored. Current widgetData:', widgetData.value);
        }
    } catch (error) {
        console.error('Failed to load widget data:', error);
    } finally {
        loadingWidgets.value.delete(widgetKey);
        console.log('Loading complete for widget:', widgetKey);
        console.log('Loading widgets:', Array.from(loadingWidgets.value));
    }
};

// Load all widget data on mount
onMounted(async () => {
    console.log('Dashboard loaded with widgets:', props.dashboard.widgets);

    for (const widget of props.dashboard.widgets) {
        console.log('Widget:', widget);
        if (widget.query_id) {
            await loadWidgetData(widget);
        }
    }
});

// Calculate grid position
const getGridStyle = (widget: Widget) => {
    const cellWidth = 100 / 12; // 12 columns
    const cellHeight = 80; // 80px per row

    // Ensure layout exists with default values
    const layout = widget.layout || { x: 0, y: 0, w: 4, h: 4 };

    return {
        position: 'absolute',
        left: `${layout.x * cellWidth}%`,
        top: `${layout.y * cellHeight}px`,
        width: `${layout.w * cellWidth}%`,
        height: `${layout.h * cellHeight}px`,
        padding: '0.5rem',
    };
};

// Format chart data based on widget config
const formatChartData = (queryResult: any, config: any) => {
    console.log('formatChartData called with:', { queryResult, config });

    if (!queryResult || !queryResult.data || queryResult.data.length === 0) {
        console.warn('No data to format');
        return [];
    }

    const { xAxis, yAxis, series, chartType } = config || {};

    // If no axis configuration, auto-detect based on data
    let xCol = xAxis;
    let yCol = yAxis;

    if (!xCol || !yCol) {
        // Auto-detect columns based on query data
        const columns = (queryResult.columns || []).map((col: any) => (typeof col === 'string' ? col : col?.name || '')).filter(Boolean);

        console.log('Available columns:', columns);
        console.log('First data row:', queryResult.data[0]);

        // If we have only one column, it might be a grouped result
        if (columns.length === 1 && queryResult.data.length > 0) {
            // Check the structure of the first data item
            const firstRow = queryResult.data[0];
            const rowKeys = Object.keys(firstRow);
            console.log('Row keys:', rowKeys);

            // Use the actual keys from the data
            if (rowKeys.length >= 2) {
                xCol = rowKeys[0];
                yCol = rowKeys[1];
            } else if (rowKeys.length === 1) {
                // Single column - create a value distribution chart
                console.log('Single column data, creating value distribution');
                const columnName = rowKeys[0];

                // Count occurrences of each value
                const valueCounts: Record<string, number> = {};
                queryResult.data.forEach((row: any) => {
                    const value = String(row[columnName] || 'Unknown');
                    valueCounts[value] = (valueCounts[value] || 0) + 1;
                });

                // Convert to chart data format
                return Object.entries(valueCounts)
                    .sort((a, b) => b[1] - a[1]) // Sort by count descending
                    .slice(0, 20) // Limit to top 20 values
                    .map(([name, value]) => ({ name, value }));
            }
        } else {
            // For this specific query with PT column, use it as x-axis
            if (columns.includes('PT')) {
                xCol = 'PT';
                yCol = 'rv_amt_accomm_gross';
            } else {
                // Generic auto-detection
                if (!xCol && columns.length > 0) {
                    xCol = columns[0];
                }
                if (!yCol && columns.length > 1) {
                    // Find a numeric-looking column
                    yCol =
                        columns.find(
                            (col: string) =>
                                col.includes('amount') ||
                                col.includes('amt') ||
                                col.includes('count') ||
                                col.includes('total') ||
                                col.includes('sum'),
                        ) || columns[1];
                }
            }
        }
    }

    // Handle column format (could be string or object with name property)
    const getColumnName = (col: any) => {
        if (!col) return '';
        return typeof col === 'string' ? col : col.name || '';
    };
    xCol = getColumnName(xCol);
    yCol = getColumnName(yCol);

    // If still no columns, return empty data
    if (!xCol && !yCol) {
        console.warn('No valid columns found for chart');
        return [];
    }

    // For pie charts, return simple name/value pairs
    if (chartType === 'pie') {
        // Aggregate data for pie chart
        const aggregated: Record<string, number> = {};
        queryResult.data.forEach((row: any) => {
            const key = String(row[xCol] || '');
            const value = Number(row[yCol] || 0);
            aggregated[key] = (aggregated[key] || 0) + value;
        });

        return Object.entries(aggregated).map(([name, value]) => ({ name, value }));
    }

    // For bar and line charts, support both simple and grouped data
    const xValues = [...new Set(queryResult.data.map((row: any) => String(row[xCol])))];

    if (series && series.length > 0) {
        // Multiple series - return grouped data format
        const seriesData = series.map((seriesCol: string) => ({
            name: seriesCol,
            data: xValues.map((x) => {
                const row = queryResult.data.find((r: any) => String(r[xCol]) === x);
                return row ? Number(row[seriesCol]) || 0 : 0;
            }),
        }));

        return {
            categories: xValues,
            series: seriesData,
        };
    } else {
        // Single series - check if we should return grouped format
        // If we have rv_amt_accomm_gross column, create a grouped chart
        const hasAccommColumns = queryResult.columns.some((col: any) => {
            const colName = typeof col === 'string' ? col : col?.name || '';
            return colName.includes('rv_amt_accomm_gross');
        });

        if (hasAccommColumns) {
            // Create series for rv_amt_accomm_gross column
            return {
                categories: xValues,
                series: [
                    {
                        name: 'rv_amt_accomm_gross',
                        data: xValues.map((x) => {
                            const row = queryResult.data.find((r: any) => String(r[xCol]) === x);
                            return row ? Number(row['rv_amt_accomm_gross']) || 0 : 0;
                        }),
                    },
                ],
            };
        }

        // Simple format for basic charts
        return queryResult.data.map((row: any) => ({
            name: String(row[xCol] || ''),
            value: Number(row[yCol] || 0),
        }));
    }
};
</script>

<template>
    <Head :title="dashboard.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ dashboard.name }}</h1>
                    <p v-if="dashboard.description" class="text-muted-foreground">
                        {{ dashboard.description }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" size="icon">
                        <Star :class="['h-4 w-4', dashboard.is_favorite ? 'fill-yellow-400 text-yellow-400' : '']" />
                    </Button>
                    <Button variant="outline">
                        <Share2 class="mr-2 h-4 w-4" />
                        Share
                    </Button>
                    <Button as-child>
                        <Link :href="route('dashboards.edit', dashboard.id)">
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="flex-1 overflow-auto rounded-lg bg-gray-100 p-4 dark:bg-gray-800">
                <div class="relative min-h-[800px]" v-if="dashboard.widgets.length > 0">
                    <!-- Render widgets -->
                    <div v-for="widget in dashboard.widgets" :key="widget.id" :style="getGridStyle(widget)">
                        <Card class="h-full">
                            <CardHeader class="pb-3">
                                <CardTitle class="text-sm font-medium">
                                    {{ widget.title || 'Untitled Widget' }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="h-[calc(100%-4rem)] overflow-hidden">
                                <div v-if="loadingWidgets.has(String(widget.id))" class="flex h-full items-center justify-center">
                                    <div class="text-muted-foreground">Loading...</div>
                                </div>
                                <div v-else-if="widgetData[String(widget.id)]" class="h-full">
                                    <!-- Chart Widget -->
                                    <component
                                        v-if="widget.type === 'chart' && widget.config?.chartType && widgetData[String(widget.id)]"
                                        :is="
                                            {
                                                bar: BarChart,
                                                line: LineChart,
                                                pie: PieChart,
                                                scatter: ScatterChart,
                                            }[widget.config.chartType]
                                        "
                                        :data="formatChartData(widgetData[String(widget.id)], widget.config)"
                                        :config="widget.config"
                                        class="h-full w-full"
                                    />

                                    <!-- Table Widget -->
                                    <div v-else-if="widget.type === 'table'" class="h-full overflow-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        v-for="col in widgetData[String(widget.id)].columns"
                                                        :key="col"
                                                        class="px-4 py-2 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                                    >
                                                        {{ col }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                <tr v-for="(row, idx) in widgetData[String(widget.id)].data.slice(0, 10)" :key="idx">
                                                    <td
                                                        v-for="col in widgetData[String(widget.id)].columns"
                                                        :key="col"
                                                        class="px-4 py-2 text-sm whitespace-nowrap text-gray-900"
                                                    >
                                                        {{ row[col] }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Metric Widget -->
                                    <div v-else-if="widget.type === 'metric'" class="flex h-full items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-4xl font-bold text-gray-900">
                                                {{ widgetData[String(widget.id)].data[0]?.[widgetData[String(widget.id)].columns[0]] || 'N/A' }}
                                            </div>
                                            <div class="mt-2 text-sm text-muted-foreground">
                                                {{ widgetData[String(widget.id)].columns[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="flex h-full items-center justify-center text-center text-muted-foreground">No data available</div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
                <div v-else class="flex h-96 items-center justify-center text-muted-foreground">
                    <div class="text-center">
                        <p class="mb-4">No widgets in this dashboard yet</p>
                        <Button as-child>
                            <Link :href="route('dashboards.edit', dashboard.id)">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit Dashboard
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
