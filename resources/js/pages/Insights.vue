<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowRight, Brain, Lightbulb, Send, Sparkles, TrendingUp } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Insights',
        href: '/insights',
    },
];

const query = ref('');

const sampleQuestions = [
    'What were our top performing products last month?',
    'Show me revenue trends by region',
    'Which customers have the highest lifetime value?',
    "What's the average order value this quarter?",
];

const handleSubmit = () => {
    // TODO: Implement AI query handling
    console.log('Query:', query.value);
};
</script>

<template>
    <Head title="AI Insights" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="text-center">
                <div class="mb-4 inline-flex items-center justify-center rounded-full bg-primary/10 p-3">
                    <Sparkles class="h-8 w-8 text-primary" />
                </div>
                <h1 class="text-3xl font-bold tracking-tight">AI-Powered Insights</h1>
                <p class="mx-auto mt-2 max-w-2xl text-muted-foreground">
                    Ask questions about your data in natural language and get instant visualizations and answers
                </p>
            </div>

            <!-- Query Input -->
            <form @submit.prevent="handleSubmit" class="mx-auto w-full max-w-3xl">
                <div class="relative">
                    <Input v-model="query" placeholder="Ask a question about your data..." class="h-12 pr-12 text-base" />
                    <Button type="submit" size="icon" class="absolute top-1 right-1 h-10 w-10" :disabled="!query.trim()">
                        <Send class="h-4 w-4" />
                    </Button>
                </div>
            </form>

            <!-- Sample Questions -->
            <div class="mx-auto w-full max-w-3xl">
                <p class="mb-3 text-sm text-muted-foreground">Try asking:</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <Button
                        v-for="question in sampleQuestions"
                        :key="question"
                        variant="outline"
                        class="h-auto justify-start px-4 py-3 text-left"
                        @click="query = question"
                    >
                        <ArrowRight class="mr-2 h-4 w-4 shrink-0" />
                        <span class="text-sm">{{ question }}</span>
                    </Button>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <Brain class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Natural Language</CardTitle>
                        <CardDescription> Ask questions the way you think, no SQL required </CardDescription>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <TrendingUp class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Smart Visualizations</CardTitle>
                        <CardDescription> Automatically generates the best charts for your data </CardDescription>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <Lightbulb class="mb-2 h-8 w-8 text-primary" />
                        <CardTitle>Proactive Insights</CardTitle>
                        <CardDescription> Discover trends and anomalies you didn't know existed </CardDescription>
                    </CardHeader>
                </Card>
            </div>

            <!-- Results Area (placeholder) -->
            <Card class="mt-4 flex-1">
                <CardContent class="flex h-full min-h-[300px] items-center justify-center text-muted-foreground">
                    <div class="text-center">
                        <Sparkles class="mx-auto mb-4 h-12 w-12 opacity-20" />
                        <p>Your insights will appear here</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
