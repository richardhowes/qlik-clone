<script setup lang="ts">
import BaseChart from './BaseChart.vue';
import { LineChart } from 'echarts/charts';
import { use } from 'echarts/core';
import type { EChartsOption } from 'echarts';
import { computed } from 'vue';

// Register line chart
use([LineChart]);

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
    smooth?: boolean;
    area?: boolean;
    showSymbol?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    height: '400px',
    loading: false,
    smooth: true,
    area: false,
    showSymbol: true,
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
            name: s.name,
            type: 'line',
            data: s.data,
            smooth: props.smooth,
            areaStyle: props.area ? {} : undefined,
            showSymbol: props.showSymbol,
        }));
    } else {
        // Simple format
        xAxisData = props.data.map(item => item.name);
        series = [{
            type: 'line',
            data: props.data.map(item => item.value),
            smooth: props.smooth,
            areaStyle: props.area ? {} : undefined,
            showSymbol: props.showSymbol,
        }];
    }

    const baseOption: EChartsOption = {
        title: props.title ? {
            text: props.title,
            left: 'center',
        } : undefined,
        tooltip: {
            trigger: 'axis',
        },
        legend: isSeriesData(props.data) && props.data.series.length > 1 ? {
            bottom: 0,
        } : undefined,
        xAxis: {
            type: 'category',
            data: xAxisData,
            name: props.xAxisLabel,
            boundaryGap: false,
        },
        yAxis: {
            type: 'value',
            name: props.yAxisLabel,
        },
        series,
    };

    return baseOption;
});
</script>

<template>
    <BaseChart :option="option" :height="height" :loading="loading" /></template>