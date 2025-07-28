<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Plus, Sparkles, TrendingUp } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Welcome Section -->
            <div class="mb-4">
                <h1 class="text-3xl font-bold tracking-tight">Welcome to your BI Platform</h1>
                <p class="text-muted-foreground">Explore your data with AI-powered insights and interactive dashboards</p>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <BarChart3 class="mb-4 h-10 w-10 text-primary" />
                    <h3 class="mb-2 font-semibold">Create Dashboard</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Build interactive visualizations from your data sources</p>
                    <Button as-child size="sm">
                        <Link :href="route('dashboards.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            New Dashboard
                        </Link>
                    </Button>
                </div>

                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <Sparkles class="mb-4 h-10 w-10 text-primary" />
                    <h3 class="mb-2 font-semibold">AI Insights</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Ask questions about your data in natural language</p>
                    <Button as-child size="sm" variant="outline">
                        <Link :href="route('insights')"> Explore Insights </Link>
                    </Button>
                </div>

                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <TrendingUp class="mb-4 h-10 w-10 text-primary" />
                    <h3 class="mb-2 font-semibold">Recent Activity</h3>
                    <p class="mb-4 text-sm text-muted-foreground">View your recently accessed dashboards and reports</p>
                    <Button as-child size="sm" variant="outline">
                        <Link :href="route('dashboards.index')"> Browse All </Link>
                    </Button>
                </div>
            </div>

            <!-- Recent Dashboards Preview -->
            <div class="mt-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Recent Dashboards</h2>
                    <Button as-child variant="ghost" size="sm">
                        <Link :href="route('dashboards.index')"> View All </Link>
                    </Button>
                </div>
                <div class="relative min-h-[400px] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
