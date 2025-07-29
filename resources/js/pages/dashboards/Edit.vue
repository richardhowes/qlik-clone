<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { BarChart, LineChart, PieChart, ScatterChart } from '@/components/charts';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { GridLayout, GridItem } from 'vue3-grid-layout-next';
import { Save, Plus, Settings, Trash2, GripVertical } from 'lucide-vue-next';
import { ref, computed, onMounted, nextTick } from 'vue';
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
    id?: number;
    dashboard_id?: number;
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
    query?: Query;
    savedQuery?: Query;
}

interface Dashboard {
    id: number;
    name: string;
    description: string | null;
    widgets: Widget[];
}

interface Props {
    dashboard: Dashboard;
    availableQueries: Query[];
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
    {
        title: 'Edit',
        href: route('dashboards.edit', props.dashboard.id),
    },
];

// State
const layout = ref<any[]>([]);
const widgets = ref<Widget[]>([]);
const showAddWidget = ref(false);
const showConfigureWidget = ref(false);
const selectedWidget = ref<Widget | null>(null);
const saving = ref(false);
const isDragging = ref(false);

// New widget form
const newWidget = ref<Partial<Widget>>({
    type: 'chart',
    title: '',
    query_id: null,
    config: {
        chartType: 'bar',
    },
});

// Grid settings
const colNum = 12;
const rowHeight = 80;
const maxRows = 20;


// Save dashboard
const saveDashboard = async () => {
    saving.value = true;
    try {
        await axios.put(route('dashboards.update', props.dashboard.id), {
            name: props.dashboard.name,
            description: props.dashboard.description,
            config: {
                colNum,
                rowHeight,
            },
        });
        
        // Update widget layouts
        for (const widget of widgets.value) {
            const layoutItem = layout.value.find(item => 
                String(widget.id) === item.i
            );
            if (layoutItem && widget.id) {
                await axios.put(
                    route('dashboards.widgets.update', [props.dashboard.id, widget.id]),
                    {
                        layout: {
                            x: layoutItem.x,
                            y: layoutItem.y,
                            w: layoutItem.w,
                            h: layoutItem.h,
                            i: layoutItem.i,
                        },
                    }
                );
            }
        }


        router.visit(route('dashboards.show', props.dashboard.id));
    } catch (error) {
        console.error('Failed to save dashboard:', error);
    } finally {
        saving.value = false;
    }
};

// Add widget
const addWidget = async () => {
    if (!newWidget.value.query_id) return;

    try {
        console.log('Adding widget:', newWidget.value);
        
        const response = await axios.post(route('dashboards.widgets.store', props.dashboard.id), {
            ...newWidget.value,
            layout: {
                x: 0,
                y: 0,
                w: 4,
                h: 4,
                i: `temp-${Date.now()}`,
            },
        });

        const widget = response.data.widget;
        console.log('Widget created:', widget);
        
        // Add query info to widget
        if (widget.query_id && !widget.query) {
            widget.query = props.availableQueries.find(q => q.id === widget.query_id);
        }
        
        // Add widget to list
        widgets.value.push(widget);
        
        // Find next available position
        const existingPositions = layout.value.map(item => ({ x: item.x, y: item.y }));
        let x = 0, y = 0;
        
        // Find first empty slot
        for (let row = 0; row < maxRows; row++) {
            for (let col = 0; col < colNum - 3; col += 4) {
                const positionTaken = existingPositions.some(pos => 
                    pos.x === col && pos.y === row * 4
                );
                if (!positionTaken) {
                    x = col;
                    y = row * 4;
                    break;
                }
            }
            if (x !== 0 || y !== 0) break;
        }
        
        const newLayoutItem = {
            x: x,
            y: y,
            w: 4,
            h: 4,
            i: String(widget.id),
        };
        
        // Force reactivity update
        layout.value = [...layout.value, newLayoutItem];
        
        await nextTick();
        console.log('Updated layout:', layout.value);
        console.log('Current widgets:', widgets.value);
        
        // Load data for the new widget
        if (widget.query_id) {
            loadWidgetData(widget);
        }

        showAddWidget.value = false;
        newWidget.value = {
            type: 'chart',
            title: '',
            query_id: null,
            config: {
                chartType: 'bar',
            },
        };
    } catch (error) {
        console.error('Failed to add widget:', error);
        alert('Failed to add widget. Check console for details.');
    }
};

// Configure widget
const configureWidget = (widget: Widget) => {
    selectedWidget.value = widget;
    showConfigureWidget.value = true;
};

// Update widget
const updateWidget = async () => {
    if (!selectedWidget.value?.id) return;

    try {
        await axios.put(
            route('dashboards.widgets.update', [props.dashboard.id, selectedWidget.value.id]),
            {
                title: selectedWidget.value.title,
                config: selectedWidget.value.config,
            }
        );

        const index = widgets.value.findIndex(w => w.id === selectedWidget.value?.id);
        if (index !== -1) {
            widgets.value[index] = { ...selectedWidget.value };
        }

        showConfigureWidget.value = false;
    } catch (error) {
        console.error('Failed to update widget:', error);
    }
};

// Delete widget
const deleteWidget = async (widget: Widget) => {
    if (!widget.id || !confirm('Are you sure you want to delete this widget?')) return;

    try {
        await axios.delete(route('dashboards.widgets.destroy', [props.dashboard.id, widget.id]));
        
        widgets.value = widgets.value.filter(w => w.id !== widget.id);
        layout.value = layout.value.filter(item => item.i !== String(widget.id));
    } catch (error) {
        console.error('Failed to delete widget:', error);
    }
};

// Layout updated
const layoutUpdated = (newLayout: any[]) => {
    layout.value = newLayout;
    console.log('Layout updated:', newLayout);
};

// Execute query to get data for widget
const executeQuery = async (widget: Widget) => {
    if (!widget.query_id) return null;

    try {
        const query = widget.savedQuery || widget.query || props.availableQueries.find(q => q.id === widget.query_id);
        if (!query) {
            console.error('Query not found for widget:', widget);
            return null;
        }

        console.log('Executing query for widget:', widget.id, 'Query:', query);
        
        // Get the data source ID from the query
        const dataSourceId = query.data_source?.id || query.data_source_id;
        if (!dataSourceId) {
            console.error('No data source ID found for query:', query);
            return null;
        }
        
        const response = await axios.post(
            route('query.execute', dataSourceId),
            {
                sql: query.sql,
                limit: 1000,
            }
        );

        return response.data;
    } catch (error) {
        console.error('Failed to execute query:', error);
        return null;
    }
};

// Helper to find widget for layout item
const getWidgetForItem = (item: any) => {
    const widget = widgets.value.find(w => String(w.id) === item.i);
    return widget;
};

// Format chart data based on widget config
const formatChartData = (queryResult: any, config: any) => {
    if (!queryResult || !queryResult.data || queryResult.data.length === 0) {
        return [];
    }

    const { xAxis, yAxis, series, chartType } = config || {};
    
    // If no axis configuration, auto-detect based on data
    let xCol = xAxis;
    let yCol = yAxis;
    
    if (!xCol || !yCol) {
        // Auto-detect columns based on query data
        const columns = (queryResult.columns || []).map((col: any) => 
            typeof col === 'string' ? col : (col?.name || '')
        ).filter(Boolean);
        
        // If we have only one column, it might be a grouped result
        if (columns.length === 1 && queryResult.data.length > 0) {
            // Check the structure of the first data item
            const firstRow = queryResult.data[0];
            const rowKeys = Object.keys(firstRow);
            
            // Use the actual keys from the data
            if (rowKeys.length >= 2) {
                xCol = rowKeys[0];
                yCol = rowKeys[1];
            } else if (rowKeys.length === 1) {
                // Single column - create a value distribution chart
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
            // Generic auto-detection
            if (!xCol && columns.length > 0) {
                xCol = columns[0];
            }
            if (!yCol && columns.length > 1) {
                yCol = columns[1];
            }
        }
    }
    
    // Handle column format (could be string or object with name property)
    const getColumnName = (col: any) => {
        if (!col) return '';
        return typeof col === 'string' ? col : (col.name || '');
    };
    xCol = getColumnName(xCol);
    yCol = getColumnName(yCol);
    
    // If still no columns, return empty data
    if (!xCol && !yCol) {
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
            data: xValues.map(x => {
                const row = queryResult.data.find((r: any) => String(r[xCol]) === x);
                return row ? (Number(row[seriesCol]) || 0) : 0;
            }),
        }));
        
        return {
            categories: xValues,
            series: seriesData,
        };
    } else {
        // Simple format for basic charts
        return queryResult.data.map((row: any) => ({
            name: String(row[xCol] || ''),
            value: Number(row[yCol] || 0)
        }));
    }
};

// Get widget data - execute query and return visualization data
const widgetData = ref<Record<string, any>>({});
const loadingWidgets = ref<Set<string>>(new Set());

const loadWidgetData = async (widget: Widget) => {
    if (!widget.query_id || !widget.id) return;
    
    const widgetKey = String(widget.id);
    loadingWidgets.value.add(widgetKey);
    
    try {
        const data = await executeQuery(widget);
        console.log(`Loaded data for widget ${widgetKey}:`, data);
        if (data) {
            widgetData.value[widgetKey] = data;
        }
    } catch (error) {
        console.error('Failed to load widget data:', error);
    } finally {
        loadingWidgets.value.delete(widgetKey);
    }
};

// Load data for all widgets on mount
onMounted(async () => {
    widgets.value = props.dashboard.widgets || [];
    
    // Ensure each widget has a query property
    widgets.value.forEach((widget) => {
        if (widget.query_id && !widget.savedQuery && !widget.query) {
            widget.query = props.availableQueries.find(q => q.id === widget.query_id);
        }
    });
    
    // Create proper layout items with correct structure
    const layoutItems = widgets.value.map((widget, index) => ({
        x: widget.layout?.x ?? (index % 3) * 4,
        y: widget.layout?.y ?? Math.floor(index / 3) * 4,
        w: widget.layout?.w ?? 4,
        h: widget.layout?.h ?? 4,
        i: String(widget.id), // Use string ID directly
    }));
    
    layout.value = layoutItems;
    
    console.log('Initialized widgets:', widgets.value);
    console.log('Initialized layout:', layout.value);
    
    // Load data for all widgets
    for (const widget of widgets.value) {
        if (widget.query_id) {
            await loadWidgetData(widget);
        }
    }
});
</script>

<template>
    <Head :title="`Edit Dashboard - ${dashboard.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Edit Dashboard</h1>
                    <p class="text-muted-foreground">
                        Drag and drop widgets to customize your dashboard
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <Button variant="outline" @click="showAddWidget = true">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Widget
                    </Button>
                    <Button @click="saveDashboard" :disabled="saving">
                        <Save class="mr-2 h-4 w-4" />
                        {{ saving ? 'Saving...' : 'Save Dashboard' }}
                    </Button>
                </div>
            </div>

            <!-- Grid Layout -->
            <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 overflow-auto">
                <div class="min-h-[800px]" style="position: relative; width: 100%;">
                    <GridLayout
                        :layout="layout"
                        :col-num="colNum"
                        :row-height="rowHeight"
                        :max-rows="maxRows"
                        :is-draggable="true"
                        :is-resizable="true"
                        :is-mirrored="false"
                        :vertical-compact="true"
                        :margin="[10, 10]"
                        :use-css-transforms="true"
                        :auto-size="true"
                        @layout-updated="layoutUpdated"
                    >
                        <GridItem
                            v-for="item in layout"
                            :key="item.i"
                            :x="item.x"
                            :y="item.y"
                            :w="item.w"
                            :h="item.h"
                            :i="item.i"
                        >
                        <div
                            class="h-full flex flex-col bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden"
                        >
                            <div class="flex items-center justify-between p-3 border-b">
                                <div class="flex items-center gap-2">
                                    <GripVertical class="h-4 w-4 text-muted-foreground cursor-move" />
                                    <h3 class="font-medium text-sm">
                                        {{ getWidgetForItem(item)?.title || 'Untitled Widget' }}
                                    </h3>
                                </div>
                                <div class="flex gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="configureWidget(getWidgetForItem(item)!)"
                                    >
                                        <Settings class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="deleteWidget(getWidgetForItem(item)!)"
                                    >
                                        <Trash2 class="h-4 w-4 text-red-500" />
                                    </Button>
                                </div>
                            </div>
                            <div class="flex-1 p-4 overflow-hidden">
                                <div v-if="getWidgetForItem(item)" class="h-full">
                                    <div v-if="loadingWidgets.has(item.i)" class="flex items-center justify-center h-full">
                                        <div class="text-muted-foreground">Loading...</div>
                                    </div>
                                    <div v-else-if="widgetData[item.i]" class="h-full">
                                        <!-- Chart Widget -->
                                        <component
                                            v-if="getWidgetForItem(item)?.type === 'chart' && getWidgetForItem(item)?.config?.chartType"
                                            :is="{
                                                bar: BarChart,
                                                line: LineChart,
                                                pie: PieChart,
                                                scatter: ScatterChart
                                            }[getWidgetForItem(item)!.config.chartType]"
                                            :data="formatChartData(widgetData[item.i], getWidgetForItem(item)!.config)"
                                            :config="getWidgetForItem(item)!.config"
                                            class="w-full h-full"
                                        />
                                        
                                        <!-- Table Widget -->
                                        <div v-else-if="getWidgetForItem(item)?.type === 'table'" class="overflow-auto h-full">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            v-for="col in widgetData[item.i].columns"
                                                            :key="col"
                                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            {{ col }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr v-for="(row, idx) in widgetData[item.i].data.slice(0, 10)" :key="idx">
                                                        <td
                                                            v-for="col in widgetData[item.i].columns"
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
                                        <div v-else-if="getWidgetForItem(item)?.type === 'metric'" class="flex items-center justify-center h-full">
                                            <div class="text-center">
                                                <div class="text-4xl font-bold text-gray-900">
                                                    {{ widgetData[item.i].data[0]?.[widgetData[item.i].columns[0]] || 'N/A' }}
                                                </div>
                                                <div class="text-sm text-muted-foreground mt-2">
                                                    {{ widgetData[item.i].columns[0] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center text-muted-foreground h-full flex items-center justify-center">
                                        No data available
                                    </div>
                                </div>
                            </div>
                        </div>
                        </GridItem>
                    </GridLayout>

                    <div v-if="widgets.length === 0" class="flex items-center justify-center h-96 text-muted-foreground">
                        Click "Add Widget" to start building your dashboard
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Widget Dialog -->
        <Dialog v-model:open="showAddWidget">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Add Widget</DialogTitle>
                    <DialogDescription>
                        Select a query to visualize on your dashboard
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4">
                    <div>
                        <Label>Query</Label>
                        <Select 
                            :model-value="String(newWidget.query_id || '')"
                            @update:model-value="(value) => newWidget.query_id = value ? Number(value) : null"
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a query" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="query in availableQueries"
                                    :key="query.id"
                                    :value="String(query.id)"
                                >
                                    {{ query.name }} ({{ query.data_source.name }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div>
                        <Label>Widget Type</Label>
                        <Select 
                            :model-value="newWidget.type"
                            @update:model-value="(value) => newWidget.type = value"
                        >
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="chart">Chart</SelectItem>
                                <SelectItem value="table">Table</SelectItem>
                                <SelectItem value="metric">Metric</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div v-if="newWidget.type === 'chart'">
                        <Label>Chart Type</Label>
                        <Select 
                            :model-value="newWidget.config!.chartType"
                            @update:model-value="(value) => newWidget.config!.chartType = value"
                        >
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

                    <div>
                        <Label>Title</Label>
                        <Input
                            v-model="newWidget.title"
                            type="text"
                            placeholder="Widget title"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <Button variant="outline" @click="showAddWidget = false">
                        Cancel
                    </Button>
                    <Button @click="addWidget" :disabled="!newWidget.query_id">
                        Add Widget
                    </Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Configure Widget Dialog -->
        <Dialog v-model:open="showConfigureWidget" v-if="selectedWidget">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Configure Widget</DialogTitle>
                    <DialogDescription>
                        Update widget settings and visualization options
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4">
                    <div>
                        <Label>Title</Label>
                        <Input
                            v-model="selectedWidget.title"
                            type="text"
                            placeholder="Widget title"
                        />
                    </div>

                    <div v-if="selectedWidget.type === 'chart'">
                        <Label>Chart Type</Label>
                        <Select v-model="selectedWidget.config.chartType">
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
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <Button variant="outline" @click="showConfigureWidget = false">
                        Cancel
                    </Button>
                    <Button @click="updateWidget">
                        Update Widget
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style>
/* Grid background pattern */
.vue-grid-layout {
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    background-position: -1px -1px;
    position: relative !important;
    min-height: 600px;
}

/* Vue Grid Layout core styles */
.vue-grid-item {
    transition: all 200ms ease;
    transition-property: left, top, right;
}

.vue-grid-item.vue-grid-placeholder {
    background: #3b82f6;
    opacity: 0.2;
    transition-duration: 100ms;
    z-index: 2;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    -o-user-select: none;
    user-select: none;
}

.vue-grid-item.cssTransforms {
    transition-property: transform;
}

.vue-grid-item.resizing {
    opacity: 0.9;
}

.vue-grid-item.vue-draggable-dragging {
    opacity: 0.7;
    z-index: 3;
}

.vue-grid-item > .vue-resizable-handle {
    position: absolute;
    width: 20px;
    height: 20px;
    bottom: 0;
    right: 0;
    cursor: se-resize;
}

.vue-grid-item > .vue-resizable-handle::after {
    content: "";
    position: absolute;
    right: 3px;
    bottom: 3px;
    width: 5px;
    height: 5px;
    border-right: 2px solid rgba(0, 0, 0, 0.4);
    border-bottom: 2px solid rgba(0, 0, 0, 0.4);
}

.vue-resizable {
    position: relative;
}

.vue-resizable-handle {
    z-index: 5;
    position: absolute;
    width: 20px;
    height: 20px;
    bottom: 0;
    right: 0;
    background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pg08IS0tIEdlbmVyYXRvcjogQWRvYmUgRmlyZXdvcmtzIENTNiwgRXhwb3J0IFNWRyBFeHRlbnNpb24gYnkgQWFyb24gQmVhbGwgKGh0dHA6Ly9maXJld29ya3MuYWJlYWxsLmNvbSkgLiBWZXJzaW9uOiAwLjYuMSAgLS0+DTwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DTxzdmcgaWQ9IlVudGl0bGVkLVBhZ2UlMjAxIiB2aWV3Qm94PSIwIDAgNiA2IiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZmZmZmZmMDAiIHZlcnNpb249IjEuMSINCXhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiDQl4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjZweCIgaGVpZ2h0PSI2cHgiDT4NCTxnIG9wYWNpdHk9IjAuMzAyIj4NCQk8cGF0aCBkPSJNIDYgNiBMIDAgNiBMIDAgNC4yIEwgNCA0LjIgTCA0LjIgNC4yIEwgNC4yIDAgTCA2IDAgTCA2IDYgTCA2IDYgWiIgZmlsbD0iIzAwMDAwMCIvPg0JPC9nPg08L3N2Zz4=');
    background-position: bottom right;
    padding: 0 3px 3px 0;
    background-repeat: no-repeat;
    background-origin: content-box;
    box-sizing: border-box;
    cursor: se-resize;
}

/* Make sure widget content doesn't overflow */
.vue-grid-item > div {
    height: 100%;
    width: 100%;
}
</style>