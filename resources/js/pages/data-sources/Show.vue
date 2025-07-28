<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs as TabsRoot, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle, Database, Edit, RefreshCw, Table, XCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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

interface DataSource {
    id: number;
    name: string;
    type: string;
    status: 'active' | 'inactive' | 'error';
    last_sync_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    dataSource: DataSource;
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
];

const testing = ref(false);
const selectedTable = ref<string | null>(null);

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'active':
            return CheckCircle;
        case 'error':
            return XCircle;
        default:
            return AlertCircle;
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active':
            return 'text-green-600';
        case 'error':
            return 'text-red-600';
        default:
            return 'text-yellow-600';
    }
};

const formatType = (type: string) => {
    const types: Record<string, string> = {
        mysql: 'MySQL',
        mariadb: 'MariaDB',
        postgresql: 'PostgreSQL',
    };
    return types[type] || type;
};

const testConnection = () => {
    testing.value = true;
    
    router.post(route('data-sources.test', props.dataSource.id), {}, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            testing.value = false;
        },
    });
};

const selectedTableData = computed(() => {
    if (!selectedTable.value || !props.schema) return null;
    return props.schema.tables.find(t => t.name === selectedTable.value);
});
</script>

<template>
    <Head :title="`Data Source: ${dataSource.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ dataSource.name }}</h1>
                    <p class="text-muted-foreground">
                        {{ formatType(dataSource.type) }} Database
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        @click="testConnection"
                        :disabled="testing"
                    >
                        <RefreshCw 
                            class="mr-2 h-4 w-4"
                            :class="{ 'animate-spin': testing }"
                        />
                        Test Connection
                    </Button>
                    <Button as-child>
                        <Link :href="route('data-sources.edit', dataSource.id)">
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Connection Status Card -->
            <Card>
                <CardHeader>
                    <CardTitle>Connection Status</CardTitle>
                    <CardDescription>
                        Current connection health and information
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Status</p>
                            <div class="flex items-center gap-2 mt-1">
                                <component
                                    :is="getStatusIcon(dataSource.status)"
                                    :class="['h-4 w-4', getStatusColor(dataSource.status)]"
                                />
                                <span :class="['font-medium capitalize', getStatusColor(dataSource.status)]">
                                    {{ dataSource.status }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Last Synced</p>
                            <p class="mt-1 font-medium">
                                {{ dataSource.last_sync_at 
                                    ? new Date(dataSource.last_sync_at).toLocaleString() 
                                    : 'Never' 
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Created</p>
                            <p class="mt-1 font-medium">
                                {{ new Date(dataSource.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Schema Information -->
            <Card v-if="schema && schema.tables.length > 0" class="flex-1">
                <CardHeader>
                    <CardTitle>Database Schema</CardTitle>
                    <CardDescription>
                        Tables and columns available in this data source
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <TabsRoot :default-value="schema.tables[0]?.name" @update:model-value="selectedTable = $event">
                        <TabsList class="w-full justify-start overflow-x-auto">
                            <TabsTrigger 
                                v-for="table in schema.tables" 
                                :key="table.name"
                                :value="table.name"
                            >
                                <Table class="mr-2 h-4 w-4" />
                                {{ table.name }}
                            </TabsTrigger>
                        </TabsList>
                        
                        <TabsContent 
                            v-for="table in schema.tables" 
                            :key="table.name"
                            :value="table.name"
                            class="mt-4"
                        >
                            <div class="rounded-md border">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b bg-muted/50">
                                            <th class="px-4 py-3 text-left text-sm font-medium">Column</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium">Type</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium">Nullable</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium">Default</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr 
                                            v-for="column in table.columns" 
                                            :key="column.name"
                                            class="border-b"
                                        >
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-2">
                                                    {{ column.name }}
                                                    <Badge v-if="column.primary" variant="secondary" class="text-xs">
                                                        PRIMARY
                                                    </Badge>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-mono text-muted-foreground">
                                                {{ column.type }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <Badge :variant="column.nullable ? 'outline' : 'secondary'">
                                                    {{ column.nullable ? 'YES' : 'NO' }}
                                                </Badge>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                                {{ column.default || '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </TabsContent>
                    </TabsRoot>
                </CardContent>
            </Card>

            <!-- Empty Schema State -->
            <Card v-else-if="dataSource.status === 'active'" class="flex-1">
                <CardContent class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <Database class="mx-auto h-12 w-12 text-muted-foreground/50" />
                        <h3 class="mt-4 text-lg font-semibold">No Schema Information</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Unable to retrieve schema information from this data source.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Error State -->
            <Card v-else class="flex-1">
                <CardContent class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <AlertCircle class="mx-auto h-12 w-12 text-destructive" />
                        <h3 class="mt-4 text-lg font-semibold">Connection Error</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Unable to connect to this data source. Please check your connection settings.
                        </p>
                        <Button 
                            variant="outline" 
                            class="mt-4"
                            as-child
                        >
                            <Link :href="route('data-sources.edit', dataSource.id)">
                                <Edit class="mr-2 h-4 w-4" />
                                Edit Connection
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>