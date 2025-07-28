<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Favorites',
        href: '/favorites',
    },
];
</script>

<template>
    <Head title="Favorites" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Favorite Dashboards</h1>
                <p class="text-muted-foreground">Quick access to your starred dashboards and reports</p>
            </div>

            <!-- Empty state -->
            <div class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <Star class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No favorites yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Star dashboards to add them to your favorites for quick access</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
