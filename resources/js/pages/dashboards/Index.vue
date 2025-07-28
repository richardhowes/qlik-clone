<script setup lang="ts">
import DashboardCard from '@/components/dashboard/DashboardCard.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Plus, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Dashboard {
    id: number;
    name: string;
    description?: string;
    slug: string;
    thumbnail?: string;
    is_favorite: boolean;
    updated_at: string;
    created_at: string;
}

interface Props {
    dashboards: {
        data: Dashboard[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

const searchQuery = ref('');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'All Dashboards',
        href: '/dashboards',
    },
];

const filteredDashboards = computed(() => {
    if (!searchQuery.value) return props.dashboards.data;

    const query = searchQuery.value.toLowerCase();
    return props.dashboards.data.filter(
        (dashboard) => dashboard.name.toLowerCase().includes(query) || dashboard.description?.toLowerCase().includes(query),
    );
});
</script>

<template>
    <Head title="Dashboards" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header with search and create button -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">All Dashboards</h1>
                    <p class="text-muted-foreground">Browse and manage your data visualizations</p>
                </div>

                <div class="flex gap-2">
                    <div class="relative">
                        <Search class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search dashboards..." class="w-[200px] pl-8 sm:w-[300px]" />
                    </div>
                    <Button as-child>
                        <Link :href="route('dashboards.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            New Dashboard
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="props.dashboards.data.length === 0" class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <BarChart3 class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No dashboards yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Create your first dashboard to start visualizing your data</p>
                    <Button as-child class="mt-4">
                        <Link :href="route('dashboards.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Create Dashboard
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Dashboard grid -->
            <div v-else-if="filteredDashboards.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <DashboardCard v-for="dashboard in filteredDashboards" :key="dashboard.id" :dashboard="dashboard" />
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
