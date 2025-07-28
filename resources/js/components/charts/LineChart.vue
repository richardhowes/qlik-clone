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

interface Series {
    name: string;
    data: DataItem[];
}

interface Props {
    data: DataItem[] | Series[];
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

const isMultiSeries = (data: DataItem[] | Series[]): data is Series[] => {
    return data.length > 0 && 'data' in data[0];
};

const option = computed<EChartsOption>(() => {
    let xAxisData: string[] = [];
    let series: any[] = [];

    if (isMultiSeries(props.data)) {
        // Multiple series
        const allDates = new Set<string>();
        props.data.forEach(s => {
            s.data.forEach(item => allDates.add(item.name));
        });
        xAxisData = Array.from(allDates).sort();

        series = props.data.map(s => ({
            name: s.name,
            type: 'line',
            data: s.data.map(item => item.value),
            smooth: props.smooth,
            areaStyle: props.area ? {} : undefined,
            showSymbol: props.showSymbol,
        }));
    } else {
        // Single series
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
        legend: isMultiSeries(props.data) ? {
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