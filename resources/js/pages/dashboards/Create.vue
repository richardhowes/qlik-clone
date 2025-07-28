<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

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
        title: 'Create',
        href: '/dashboards/create',
    },
];

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('dashboards.store'));
};
</script>

<template>
    <Head title="Create Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Create New Dashboard</h1>
                <p class="text-muted-foreground">Set up your dashboard details to start building visualizations</p>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Dashboard Details</CardTitle>
                    <CardDescription> Give your dashboard a name and description </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Dashboard Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Sales Performance Q4"
                                :class="{ 'border-destructive': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description (optional)</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                placeholder="Brief description of this dashboard"
                                :class="{ 'border-destructive': form.errors.description }"
                            />
                            <p v-if="form.errors.description" class="text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Button type="submit" :disabled="form.processing"> Create Dashboard </Button>
                            <Button type="button" variant="outline" @click="router.visit(route('dashboards.index'))"> Cancel </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
