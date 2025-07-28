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

interface Props {
    data: DataItem[];
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

const option = computed<EChartsOption>(() => {
    const xAxisData = props.data.map(item => item.name);
    const seriesData = props.data.map(item => item.value);

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
        series: [
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
        ],
    };

    return baseOption;
});
</script>

<template>
    <BaseChart :option="option" :height="height" :loading="loading" /></template>