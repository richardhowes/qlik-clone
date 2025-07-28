<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import ConnectionForm from '@/components/data-source/ConnectionForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    types: Array<{ value: string; label: string }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/dashboard',
    },
    {
        title: 'Data Sources',
        href: '/data-sources',
    },
    {
        title: 'Create',
        href: '/data-sources/create',
    },
];

const form = useForm({
    name: '',
    type: 'mysql',
    connection_config: {},
    test_only: false,
});

const testResult = ref<{ success: boolean; message: string } | null>(null);
const testing = ref(false);

const submit = () => {
    testResult.value = null;
    form.test_only = false;
    form.post(route('data-sources.store'), {
        onSuccess: () => {
            // Form will redirect on success
        },
    });
};

const testConnection = () => {
    testing.value = true;
    testResult.value = null;
    form.test_only = true;
    
    form.post(route('data-sources.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            testing.value = false;
            // Get the test result from flash data
            const flash = page.props.flash as any;
            if (flash?.testResult) {
                testResult.value = flash.testResult;
            }
        },
        onError: () => {
            testing.value = false;
            testResult.value = {
                success: false,
                message: 'Please fill in all required fields correctly',
            };
        },
        onFinish: () => {
            testing.value = false;
            form.test_only = false;
        },
    });
};
</script>

<template>
    <Head title="Create Data Source" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Add Data Source</h1>
                <p class="text-muted-foreground">
                    Connect a new database to start building dashboards
                </p>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Connection Details</CardTitle>
                    <CardDescription>
                        Configure your database connection settings
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="space-y-2">
                                <Label for="name">Connection Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g., Production Database"
                                    :class="{ 'border-destructive': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Type -->
                            <div class="space-y-2">
                                <Label for="type">Database Type</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger id="type">
                                        <SelectValue placeholder="Select database type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="type in types" 
                                            :key="type.value"
                                            :value="type.value"
                                        >
                                            {{ type.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.type" class="text-sm text-destructive">
                                    {{ form.errors.type }}
                                </p>
                            </div>

                            <!-- Dynamic Connection Form -->
                            <div v-if="form.type">
                                <h3 class="text-sm font-medium mb-3">Connection Settings</h3>
                                <ConnectionForm
                                    v-model="form.connection_config"
                                    :type="form.type"
                                    :errors="form.errors"
                                />
                            </div>
                        </div>

                        <!-- Test Result -->
                        <div v-if="testResult" :class="[
                            'p-4 rounded-lg border',
                            testResult.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
                        ]">
                            <div class="flex items-center gap-2">
                                <component
                                    :is="testResult.success ? CheckCircle : AlertCircle"
                                    :class="[
                                        'h-5 w-5',
                                        testResult.success ? 'text-green-600' : 'text-red-600'
                                    ]"
                                />
                                <p :class="[
                                    'text-sm font-medium',
                                    testResult.success ? 'text-green-800' : 'text-red-800'
                                ]">
                                    {{ testResult.message }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit(route('data-sources.index'))"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="testConnection"
                                :disabled="testing || !form.name || !form.type"
                            >
                                {{ testing ? 'Testing...' : 'Test Connection' }}
                            </Button>
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                            >
                                Create Data Source
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>