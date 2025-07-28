<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ConnectionForm from '@/components/data-source/ConnectionForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface DataSource {
    id: number;
    name: string;
    type: string;
    connection_config: Record<string, any>;
    status: 'active' | 'inactive' | 'error';
    last_sync_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    dataSource: DataSource;
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
        title: props.dataSource.name,
        href: route('data-sources.show', props.dataSource.id),
    },
    {
        title: 'Edit',
        href: route('data-sources.edit', props.dataSource.id),
    },
];

const form = useForm({
    name: props.dataSource.name,
    connection_config: props.dataSource.connection_config,
    test_only: false,
});

const testResult = ref<{ success: boolean; message: string } | null>(null);
const testing = ref(false);

const submit = () => {
    testResult.value = null;
    form.test_only = false;
    form.put(route('data-sources.update', props.dataSource.id), {
        onSuccess: () => {
            // Form will redirect on success
        },
    });
};

const testConnection = () => {
    testing.value = true;
    testResult.value = null;
    form.test_only = true;
    
    form.put(route('data-sources.update', props.dataSource.id), {
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

const formatType = (type: string) => {
    const types: Record<string, string> = {
        mysql: 'MySQL',
        mariadb: 'MariaDB',
        postgresql: 'PostgreSQL',
    };
    return types[type] || type;
};
</script>

<template>
    <Head :title="`Edit Data Source: ${dataSource.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Edit Data Source</h1>
                <p class="text-muted-foreground">
                    Update your {{ formatType(dataSource.type) }} connection settings
                </p>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Connection Details</CardTitle>
                    <CardDescription>
                        Modify your database connection settings
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

                            <!-- Type (Read-only) -->
                            <div class="space-y-2">
                                <Label>Database Type</Label>
                                <div class="flex items-center gap-2 p-3 rounded-md bg-muted">
                                    <span class="font-medium">{{ formatType(dataSource.type) }}</span>
                                    <span class="text-sm text-muted-foreground">
                                        (Type cannot be changed)
                                    </span>
                                </div>
                            </div>

                            <!-- Dynamic Connection Form -->
                            <div>
                                <h3 class="text-sm font-medium mb-3">Connection Settings</h3>
                                <ConnectionForm
                                    v-model="form.connection_config"
                                    :type="dataSource.type"
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
                                @click="router.visit(route('data-sources.show', dataSource.id))"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="testConnection"
                                :disabled="testing || !form.name"
                            >
                                {{ testing ? 'Testing...' : 'Test Connection' }}
                            </Button>
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                            >
                                Update Data Source
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>