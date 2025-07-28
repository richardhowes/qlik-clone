<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { FolderOpen, Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Collections',
        href: '/collections',
    },
];
</script>

<template>
    <Head title="Collections" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Collections</h1>
                    <p class="text-muted-foreground">Organize your dashboards and reports into collections</p>
                </div>
                <Button>
                    <Plus class="mr-2 h-4 w-4" />
                    New Collection
                </Button>
            </div>

            <!-- Empty state -->
            <div class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <FolderOpen class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No collections yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Create collections to organize your dashboards by project or team</p>
                    <Button class="mt-4">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Collection
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
