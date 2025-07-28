<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Database, FileSpreadsheet, Link2, Plus, Sheet } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Browse',
        href: '/browse',
    },
];

const dataSourceTypes = [
    {
        title: 'MariaDB / MySQL',
        description: 'Connect to your MariaDB or MySQL databases',
        icon: Database,
        color: 'text-blue-500',
    },
    {
        title: 'Excel Files',
        description: 'Upload and analyze Excel spreadsheets',
        icon: FileSpreadsheet,
        color: 'text-green-500',
    },
    {
        title: 'Google Sheets',
        description: 'Import data from Google Sheets',
        icon: Sheet,
        color: 'text-yellow-500',
    },
];
</script>

<template>
    <Head title="Browse Data Sources" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Data Sources</h1>
                <p class="text-muted-foreground">Connect and manage your data sources to build dashboards</p>
            </div>

            <!-- Data Source Types -->
            <div>
                <h2 class="mb-4 text-lg font-semibold">Connect New Data Source</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    <Card v-for="source in dataSourceTypes" :key="source.title" class="cursor-pointer transition-shadow hover:shadow-md">
                        <CardHeader>
                            <component :is="source.icon" :class="['mb-2 h-8 w-8', source.color]" />
                            <CardTitle>{{ source.title }}</CardTitle>
                            <CardDescription>{{ source.description }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" size="sm" class="w-full">
                                <Link2 class="mr-2 h-4 w-4" />
                                Connect
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Connected Sources -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Connected Sources</h2>
                    <Button variant="outline" size="sm">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Source
                    </Button>
                </div>

                <Card>
                    <CardContent class="flex h-32 items-center justify-center text-muted-foreground">
                        <div class="text-center">
                            <Database class="mx-auto mb-2 h-8 w-8 opacity-50" />
                            <p class="text-sm">No data sources connected yet</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
