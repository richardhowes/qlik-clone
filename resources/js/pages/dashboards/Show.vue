<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { BarChart, LineChart, PieChart, ScatterChart } from '@/components/charts';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Share2, Star } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface Query {
    id: number;
    name: string;
    sql: string;
    data_source: {
        id: number;
        name: string;
    };
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
    if (!widget.query_id || !widget.savedQuery) return null;

    try {
        const response = await axios.post(
            route('query.execute', widget.savedQuery.data_source.id),
            {
                sql: widget.savedQuery.sql,
                limit: 1000,
            }
        );

        return response.data;
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
        if (data) {
            widgetData.value[widgetKey] = data;
        }
    } catch (error) {
        console.error('Failed to load widget data:', error);
    } finally {
        loadingWidgets.value.delete(widgetKey);
    }
};

// Load all widget data on mount
onMounted(async () => {
    for (const widget of props.dashboard.widgets) {
        if (widget.query_id) {
            loadWidgetData(widget);
        }
    }
});

// Calculate grid position
const getGridStyle = (widget: Widget) => {
    const cellWidth = 100 / 12; // 12 columns
    const cellHeight = 80; // 80px per row
    
    return {
        position: 'absolute',
        left: `${widget.layout.x * cellWidth}%`,
        top: `${widget.layout.y * cellHeight}px`,
        width: `${widget.layout.w * cellWidth}%`,
        height: `${widget.layout.h * cellHeight}px`,
        padding: '0.5rem',
    };
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
            <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 overflow-auto">
                <div class="relative min-h-[800px]" v-if="dashboard.widgets.length > 0">
                    <!-- Render widgets -->
                    <div
                        v-for="widget in dashboard.widgets"
                        :key="widget.id"
                        :style="getGridStyle(widget)"
                    >
                        <Card class="h-full">
                            <CardHeader class="pb-3">
                                <CardTitle class="text-sm font-medium">
                                    {{ widget.title || 'Untitled Widget' }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="h-[calc(100%-4rem)] overflow-hidden">
                                <div v-if="loadingWidgets.has(String(widget.id))" class="flex items-center justify-center h-full">
                                    <div class="text-muted-foreground">Loading...</div>
                                </div>
                                <div v-else-if="widgetData[String(widget.id)]" class="h-full">
                                    <!-- Chart Widget -->
                                    <component
                                        v-if="widget.type === 'chart' && widget.config?.chartType"
                                        :is="{
                                            bar: BarChart,
                                            line: LineChart,
                                            pie: PieChart,
                                            scatter: ScatterChart
                                        }[widget.config.chartType]"
                                        :data="widgetData[String(widget.id)]"
                                        :config="widget.config"
                                        class="w-full h-full"
                                    />
                                    
                                    <!-- Table Widget -->
                                    <div v-else-if="widget.type === 'table'" class="overflow-auto h-full">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        v-for="col in widgetData[String(widget.id)].columns"
                                                        :key="col"
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    >
                                                        {{ col }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="(row, idx) in widgetData[String(widget.id)].rows.slice(0, 10)" :key="idx">
                                                    <td
                                                        v-for="col in widgetData[String(widget.id)].columns"
                                                        :key="col"
                                                        class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"
                                                    >
                                                        {{ row[col] }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Metric Widget -->
                                    <div v-else-if="widget.type === 'metric'" class="flex items-center justify-center h-full">
                                        <div class="text-center">
                                            <div class="text-4xl font-bold text-gray-900">
                                                {{ widgetData[String(widget.id)].rows[0]?.[widgetData[String(widget.id)].columns[0]] || 'N/A' }}
                                            </div>
                                            <div class="text-sm text-muted-foreground mt-2">
                                                {{ widgetData[String(widget.id)].columns[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center text-muted-foreground h-full flex items-center justify-center">
                                    No data available
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
                <div v-else class="flex items-center justify-center h-96 text-muted-foreground">
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
