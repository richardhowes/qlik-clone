<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowDown, ArrowUp, TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    title: string;
    value: string | number;
    description?: string;
    change?: number;
    changeLabel?: string;
    trend?: 'up' | 'down' | 'neutral';
    loading?: boolean;
    icon?: any;
    prefix?: string;
    suffix?: string;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
});

const changeColor = computed(() => {
    if (!props.change) return 'text-muted-foreground';
    return props.change > 0 ? 'text-green-600' : 'text-red-600';
});

const trendIcon = computed(() => {
    if (!props.trend || props.trend === 'neutral') return null;
    return props.trend === 'up' ? ArrowUp : ArrowDown;
});

const formattedValue = computed(() => {
    let value = props.value;
    if (typeof value === 'number') {
        // Format large numbers
        if (value >= 1000000) {
            value = `${(value / 1000000).toFixed(1)}M`;
        } else if (value >= 1000) {
            value = `${(value / 1000).toFixed(1)}K`;
        } else {
            value = value.toLocaleString();
        }
    }
    return `${props.prefix || ''}${value}${props.suffix || ''}`;
});

const formattedChange = computed(() => {
    if (!props.change) return '';
    const sign = props.change > 0 ? '+' : '';
    return `${sign}${props.change.toFixed(1)}%`;
});
</script>

<template>
    <Card class="relative overflow-hidden">
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">
                {{ title }}
            </CardTitle>
            <component :is="icon || TrendingUp" class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
            <div v-if="loading" class="space-y-2">
                <div class="h-8 w-24 animate-pulse rounded bg-muted" />
                <div class="h-4 w-32 animate-pulse rounded bg-muted" />
            </div>
            <template v-else>
                <div class="text-2xl font-bold">
                    {{ formattedValue }}
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span v-if="change" :class="changeColor" class="flex items-center gap-1">
                        <component v-if="trendIcon" :is="trendIcon" class="h-3 w-3" />
                        {{ formattedChange }}
                    </span>
                    <span v-if="changeLabel" class="text-muted-foreground">
                        {{ changeLabel }}
                    </span>
                </div>
                <CardDescription v-if="description" class="mt-2">
                    {{ description }}
                </CardDescription>
            </template>
        </CardContent>

        <!-- Optional background decoration -->
        <div v-if="icon" class="absolute -top-4 -right-4 h-20 w-20 opacity-5">
            <component :is="icon" class="h-full w-full" />
        </div>
    </Card>
</template>
