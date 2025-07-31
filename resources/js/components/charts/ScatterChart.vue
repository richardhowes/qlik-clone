<script setup lang="ts">
import { ScatterChart } from 'echarts/charts';
import { use } from 'echarts/core';
import { computed } from 'vue';
import BaseChart from './BaseChart.vue';

// Register scatter chart
use([ScatterChart]);

interface DataPoint {
    x: number;
    y: number;
    name?: string;
}

interface Props {
    data: DataPoint[];
    title?: string;
    xAxisLabel?: string;
    yAxisLabel?: string;
    height?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    xAxisLabel: 'X Axis',
    yAxisLabel: 'Y Axis',
    height: '400px',
});

const chartOptions = computed(() => ({
    title: {
        text: props.title,
        left: 'center',
    },
    tooltip: {
        trigger: 'item',
        formatter: (params: any) => {
            return `${params.data.name || 'Point'}<br/>X: ${params.data[0]}<br/>Y: ${params.data[1]}`;
        },
    },
    xAxis: {
        type: 'value',
        name: props.xAxisLabel,
        nameLocation: 'middle',
        nameGap: 30,
    },
    yAxis: {
        type: 'value',
        name: props.yAxisLabel,
        nameLocation: 'middle',
        nameGap: 40,
    },
    series: [
        {
            type: 'scatter',
            data: props.data.map((point) => [point.x, point.y, point.name]),
            symbolSize: 8,
            itemStyle: {
                color: '#3b82f6',
            },
        },
    ],
}));
</script>

<template>
    <BaseChart :option="chartOptions" :height="height" />
</template>
