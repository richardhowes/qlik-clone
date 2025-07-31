<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { formatDistanceToNow } from 'date-fns';
import { Database, ExternalLink, FileCode, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DataSource {
    id: number;
    name: string;
    type: string;
}

interface Query {
    id: number;
    name: string;
    sql: string;
    data_source_id: number;
    data_source?: DataSource;
    created_at: string;
    updated_at: string;
}

interface Props {
    queries: {
        data: Query[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    dataSource?: DataSource;
}

const props = defineProps<Props>();

const searchQuery = ref('');

const breadcrumbs: BreadcrumbItem[] = computed(() => {
    const items: BreadcrumbItem[] = [
        {
            title: 'Home',
            href: '/dashboard',
        },
    ];

    if (props.dataSource) {
        items.push(
            {
                title: 'Data Sources',
                href: '/data-sources',
            },
            {
                title: props.dataSource.name,
                href: route('data-sources.show', props.dataSource.id),
            },
            {
                title: 'Queries',
                href: route('data-sources.queries.index', props.dataSource.id),
            },
        );
    } else {
        items.push({
            title: 'All Queries',
            href: '/queries',
        });
    }

    return items;
});

const filteredQueries = computed(() => {
    if (!searchQuery.value) return props.queries.data;

    const query = searchQuery.value.toLowerCase();
    return props.queries.data.filter(
        (q) => q.name.toLowerCase().includes(query) || q.sql.toLowerCase().includes(query) || q.data_source?.name.toLowerCase().includes(query),
    );
});

const deleteQuery = (query: Query) => {
    if (confirm(`Are you sure you want to delete the query "${query.name}"?`)) {
        router.delete(route('query.destroy', query.id), {
            preserveScroll: true,
        });
    }
};

const openQuery = (query: Query) => {
    router.visit(route('query.editor.saved', [query.data_source_id, query.id]));
};
</script>

<template>
    <Head :title="dataSource ? `Queries - ${dataSource.name}` : 'All Queries'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header with search -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        {{ dataSource ? `Queries for ${dataSource.name}` : 'All Queries' }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ dataSource ? 'Saved queries for this data source' : 'Browse and manage your saved queries' }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <div class="relative">
                        <Search class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search queries..." class="w-[200px] pl-8 sm:w-[300px]" />
                    </div>
                    <Button v-if="dataSource" as-child>
                        <Link :href="route('query.editor', dataSource.id)">
                            <FileCode class="mr-2 h-4 w-4" />
                            New Query
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="props.queries.data.length === 0" class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <FileCode class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No saved queries yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ dataSource ? 'Create your first query for this data source' : 'Start by creating a query from a data source' }}
                    </p>
                    <Button v-if="dataSource" as-child class="mt-4">
                        <Link :href="route('query.editor', dataSource.id)">
                            <FileCode class="mr-2 h-4 w-4" />
                            Create Query
                        </Link>
                    </Button>
                    <Button v-else as-child class="mt-4">
                        <Link href="/data-sources">
                            <Database class="mr-2 h-4 w-4" />
                            Browse Data Sources
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Query list -->
            <div v-else-if="filteredQueries.length > 0" class="grid gap-4">
                <Card
                    v-for="query in filteredQueries"
                    :key="query.id"
                    class="cursor-pointer transition-shadow hover:shadow-md"
                    @click="openQuery(query)"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="text-lg">{{ query.name }}</CardTitle>
                                <CardDescription v-if="!dataSource && query.data_source" class="mt-1">
                                    <div class="flex items-center gap-1">
                                        <Database class="h-3 w-3" />
                                        {{ query.data_source.name }} ({{ query.data_source.type }})
                                    </div>
                                </CardDescription>
                            </div>
                            <div class="flex gap-2">
                                <Button variant="ghost" size="icon" @click.stop="openQuery(query)">
                                    <ExternalLink class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click.stop="deleteQuery(query)">
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="overflow-x-auto rounded-md bg-muted p-3 font-mono text-sm text-muted-foreground">
                                <pre class="whitespace-pre-wrap">{{ query.sql.substring(0, 200) }}{{ query.sql.length > 200 ? '...' : '' }}</pre>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Last updated {{ formatDistanceToNow(new Date(query.updated_at), { addSuffix: true }) }}
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
                    <p class="mt-2 text-sm text-muted-foreground">Try adjusting your search terms</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
