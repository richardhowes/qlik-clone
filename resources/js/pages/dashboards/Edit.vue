<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Plus } from 'lucide-vue-next';

interface Dashboard {
    id: number;
    name: string;
    description?: string;
    slug: string;
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
        href: route('dashboards.edit', props.dashboard.id),
    },
];
</script>

<template>
    <Head :title="`Edit ${dashboard.name}`" />

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
                    <Button variant="outline" as-child>
                        <Link :href="route('dashboards.show', dashboard.id)"> Preview </Link>
                    </Button>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add Widget
                    </Button>
                </div>
            </div>

            <!-- Dashboard Builder Area -->
            <Card class="flex-1">
                <CardHeader>
                    <CardTitle>Dashboard Builder</CardTitle>
                    <CardDescription> Drag and drop widgets to build your dashboard </CardDescription>
                </CardHeader>
                <CardContent class="relative min-h-[600px]">
                    <!-- Placeholder for dashboard builder -->
                    <div class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                        <div class="text-center">
                            <BarChart3 class="mx-auto mb-4 h-12 w-12 opacity-20" />
                            <p>Dashboard builder coming soon</p>
                            <p class="mt-2 text-sm">Add charts, tables, and KPIs to visualize your data</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
