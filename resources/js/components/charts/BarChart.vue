<script setup lang="ts">
import BaseChart from './BaseChart.vue';
import { BarChart } from 'echarts/charts';
import { use } from 'echarts/core';
import type { EChartsOption } from 'echarts';
import { computed } from 'vue';

// Register bar chart
use([BarChart]);

interface DataItem {
    name: string;
    value: number;
}

interface SeriesData {
    categories: string[];
    series: Array<{
        name: string;
        data: number[];
    }>;
}

interface Props {
    data: DataItem[] | SeriesData;
    title?: string;
    xAxisLabel?: string;
    yAxisLabel?: string;
    height?: string;
    loading?: boolean;
    horizontal?: boolean;
    showValues?: boolean;
    color?: string | string[];
}

const props = withDefaults(defineProps<Props>(), {
    height: '400px',
    loading: false,
    horizontal: false,
    showValues: true,
});

const isSeriesData = (data: DataItem[] | SeriesData): data is SeriesData => {
    return 'categories' in data && 'series' in data;
};

const option = computed<EChartsOption>(() => {
    let xAxisData: string[] = [];
    let series: any[] = [];

    if (isSeriesData(props.data)) {
        // Multiple series format
        xAxisData = props.data.categories;
        series = props.data.series.map(s => ({
            type: 'bar',
            name: s.name,
            data: s.data,
            label: {
                show: props.showValues,
                position: props.horizontal ? 'right' : 'top',
            },
            itemStyle: {
                color: props.color,
            },
        }));
    } else {
        // Simple format
        xAxisData = props.data.map(item => item.name);
        const seriesData = props.data.map(item => item.value);
        series = [
            {
                type: 'bar',
                data: seriesData,
                label: {
                    show: props.showValues,
                    position: props.horizontal ? 'right' : 'top',
                },
                itemStyle: {
                    color: props.color,
                },
            },
        ];
    }

    const baseOption: EChartsOption = {
        title: props.title ? {
            text: props.title,
            left: 'center',
        } : undefined,
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow',
            },
        },
        legend: isSeriesData(props.data) && props.data.series.length > 1 ? {
            bottom: 0,
        } : undefined,
        xAxis: {
            type: props.horizontal ? 'value' : 'category',
            data: props.horizontal ? undefined : xAxisData,
            name: props.xAxisLabel,
            axisLabel: {
                rotate: props.horizontal ? 0 : 45,
            },
        },
        yAxis: {
            type: props.horizontal ? 'category' : 'value',
            data: props.horizontal ? xAxisData : undefined,
            name: props.yAxisLabel,
        },
        series: series,
    };

    return baseOption;
});
</script>

<template>
    <BaseChart :option="option" :height="height" :loading="loading" /></template>