<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Share2, Star } from 'lucide-vue-next';

interface Dashboard {
    id: number;
    name: string;
    description?: string;
    slug: string;
    is_favorite: boolean;
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
            <div class="grid flex-1 gap-4">
                <!-- Placeholder for actual dashboard content -->
                <div class="relative min-h-[600px] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                        <p>Dashboard visualizations will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
