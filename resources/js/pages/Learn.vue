<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { BookOpen, FileText, PlayCircle, Users } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Learn',
        href: '/learn',
    },
];

const resources = [
    {
        title: 'Getting Started',
        description: 'Learn the basics of creating your first dashboard',
        icon: BookOpen,
        items: ['Quick Start Guide', 'Understanding Data Sources', 'Building Your First Chart', 'Sharing Dashboards'],
    },
    {
        title: 'Video Tutorials',
        description: 'Watch step-by-step video guides',
        icon: PlayCircle,
        items: ['Platform Overview', 'Advanced Visualizations', 'AI Features Walkthrough', 'Best Practices'],
    },
    {
        title: 'Documentation',
        description: 'Detailed guides and API references',
        icon: FileText,
        items: ['User Manual', 'API Documentation', 'Chart Types Reference', 'Troubleshooting'],
    },
    {
        title: 'Community',
        description: 'Connect with other users and get help',
        icon: Users,
        items: ['Discussion Forum', 'Feature Requests', 'Success Stories', 'Support Center'],
    },
];
</script>

<template>
    <Head title="Learn" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-3xl font-bold tracking-tight">Learning Center</h1>
                <p class="mt-2 text-muted-foreground">Everything you need to master data visualization and analytics</p>
            </div>

            <div class="mx-auto grid w-full max-w-4xl gap-4 md:grid-cols-2">
                <Card v-for="resource in resources" :key="resource.title">
                    <CardHeader>
                        <component :is="resource.icon" class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>{{ resource.title }}</CardTitle>
                        <CardDescription>{{ resource.description }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ul class="space-y-2">
                            <li v-for="item in resource.items" :key="item" class="flex items-center text-sm">
                                <span class="mr-2 text-muted-foreground">•</span>
                                {{ item }}
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Tips -->
            <div class="mx-auto mt-8 w-full max-w-4xl">
                <h2 class="mb-4 text-xl font-semibold">Quick Tips</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardContent class="pt-6">
                            <p class="text-sm">
                                <strong>Tip:</strong> Use natural language queries in the Insights page to quickly analyze your data without writing
                                SQL.
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <p class="text-sm">
                                <strong>Tip:</strong> Star your most-used dashboards to access them quickly from the Favorites section.
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <p class="text-sm"><strong>Tip:</strong> Organize related dashboards into collections for better project management.</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
