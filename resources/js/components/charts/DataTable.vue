<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ArrowDown, ArrowUp, ArrowUpDown, Download, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    align?: 'left' | 'center' | 'right';
    formatter?: (value: any) => string;
}

interface Props {
    title?: string;
    columns: Column[];
    data: Record<string, any>[];
    loading?: boolean;
    searchable?: boolean;
    exportable?: boolean;
    pageSize?: number;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    searchable: true,
    exportable: true,
    pageSize: 10,
});

const searchQuery = ref('');
const sortColumn = ref<string | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');
const currentPage = ref(1);

const filteredData = computed(() => {
    let result = [...props.data];

    // Search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter((row) => {
            return props.columns.some((col) => {
                const value = row[col.key];
                return value && value.toString().toLowerCase().includes(query);
            });
        });
    }

    // Sort
    if (sortColumn.value) {
        result.sort((a, b) => {
            const aVal = a[sortColumn.value!];
            const bVal = b[sortColumn.value!];

            if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1;
            return 0;
        });
    }

    return result;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * props.pageSize;
    const end = start + props.pageSize;
    return filteredData.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / props.pageSize);
});

const handleSort = (column: Column) => {
    if (!column.sortable) return;

    if (sortColumn.value === column.key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column.key;
        sortDirection.value = 'asc';
    }
};

const exportToCSV = () => {
    const headers = props.columns.map((col) => col.label).join(',');
    const rows = filteredData.value.map((row) => {
        return props.columns
            .map((col) => {
                const value = row[col.key];
                const formatted = col.formatter ? col.formatter(value) : value;
                // Escape quotes and wrap in quotes if contains comma
                const escaped = String(formatted).replace(/"/g, '""');
                return escaped.includes(',') ? `"${escaped}"` : escaped;
            })
            .join(',');
    });

    const csv = [headers, ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${props.title || 'data'}-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(url);
};

const getSortIcon = (column: Column) => {
    if (!column.sortable) return null;
    if (sortColumn.value !== column.key) return ArrowUpDown;
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <CardTitle v-if="title">{{ title }}</CardTitle>
                <div class="flex items-center gap-2">
                    <div v-if="searchable" class="relative">
                        <Search class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search..." class="w-[200px] pl-8" />
                    </div>
                    <Button v-if="exportable" variant="outline" size="sm" @click="exportToCSV">
                        <Download class="mr-2 h-4 w-4" />
                        Export
                    </Button>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b text-xs uppercase">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                :class="[
                                    'px-4 py-3 font-medium',
                                    column.sortable ? 'cursor-pointer select-none hover:bg-muted/50' : '',
                                    `text-${column.align || 'left'}`,
                                ]"
                                @click="handleSort(column)"
                            >
                                <div
                                    class="flex items-center gap-1"
                                    :class="column.align === 'right' ? 'justify-end' : column.align === 'center' ? 'justify-center' : ''"
                                >
                                    {{ column.label }}
                                    <component v-if="column.sortable" :is="getSortIcon(column)" class="h-3 w-3" />
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td :colspan="columns.length" class="py-8 text-center text-muted-foreground">Loading...</td>
                        </tr>
                        <tr v-else-if="paginatedData.length === 0">
                            <td :colspan="columns.length" class="py-8 text-center text-muted-foreground">No data available</td>
                        </tr>
                        <tr v-else v-for="(row, index) in paginatedData" :key="index" class="border-b hover:bg-muted/50">
                            <td v-for="column in columns" :key="column.key" :class="['px-4 py-3', `text-${column.align || 'left'}`]">
                                {{ column.formatter ? column.formatter(row[column.key]) : row[column.key] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ (currentPage - 1) * pageSize + 1 }} to {{ Math.min(currentPage * pageSize, filteredData.length) }} of
                    {{ filteredData.length }} results
                </p>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--"> Previous </Button>
                    <Button variant="outline" size="sm" :disabled="currentPage === totalPages" @click="currentPage++"> Next </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
