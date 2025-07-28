<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle, Database, Plus, RefreshCw, Search, Trash2, XCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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
    dataSources: {
        data: DataSource[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

const searchQuery = ref('');
const testingConnections = ref<Set<number>>(new Set());

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Data Sources',
        href: '/data-sources',
    },
];

const filteredDataSources = computed(() => {
    if (!searchQuery.value) return props.dataSources.data;
    
    const query = searchQuery.value.toLowerCase();
    return props.dataSources.data.filter(source => 
        source.name.toLowerCase().includes(query) ||
        source.type.toLowerCase().includes(query)
    );
});

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

const testConnection = (dataSource: DataSource) => {
    testingConnections.value.add(dataSource.id);
    
    router.post(route('data-sources.test', dataSource.id), {}, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            testingConnections.value.delete(dataSource.id);
        },
    });
};

const deleteDataSource = (dataSource: DataSource) => {
    if (confirm(`Are you sure you want to delete "${dataSource.name}"?`)) {
        router.delete(route('data-sources.destroy', dataSource.id));
    }
};
</script>

<template>
    <Head title="Data Sources" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Data Sources</h1>
                    <p class="text-muted-foreground">
                        Manage your database connections and data sources
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <div class="relative">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            placeholder="Search sources..."
                            class="pl-8 w-[200px] sm:w-[300px]"
                        />
                    </div>
                    <Button as-child>
                        <Link :href="route('data-sources.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Source
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="props.dataSources.data.length === 0" class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <Database class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No data sources yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Connect a database to start building dashboards
                    </p>
                    <Button as-child class="mt-4">
                        <Link :href="route('data-sources.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Data Source
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Data sources list -->
            <div v-else-if="filteredDataSources.length > 0" class="grid gap-4">
                <Card v-for="source in filteredDataSources" :key="source.id">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <Database class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <CardTitle class="text-lg">
                                        <Link 
                                            :href="route('data-sources.show', source.id)"
                                            class="hover:underline"
                                        >
                                            {{ source.name }}
                                        </Link>
                                    </CardTitle>
                                    <CardDescription>
                                        {{ formatType(source.type) }}
                                    </CardDescription>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1">
                                    <component
                                        :is="getStatusIcon(source.status)"
                                        :class="['h-4 w-4', getStatusColor(source.status)]"
                                    />
                                    <span :class="['text-sm capitalize', getStatusColor(source.status)]">
                                        {{ source.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-muted-foreground">
                                Last synced: 
                                <span v-if="source.last_sync_at">
                                    {{ new Date(source.last_sync_at).toLocaleString() }}
                                </span>
                                <span v-else>Never</span>
                            </p>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="testConnection(source)"
                                    :disabled="testingConnections.has(source.id)"
                                >
                                    <RefreshCw 
                                        class="mr-2 h-4 w-4"
                                        :class="{ 'animate-spin': testingConnections.has(source.id) }"
                                    />
                                    Test
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="route('data-sources.edit', source.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="deleteDataSource(source)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- No search results -->
            <div v-else class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <Search class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No results found</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Try adjusting your search terms
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>