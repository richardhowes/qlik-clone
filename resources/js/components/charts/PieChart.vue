<script setup lang="ts">
import type { EChartsOption } from 'echarts';
import { PieChart } from 'echarts/charts';
import { use } from 'echarts/core';
import { computed } from 'vue';
import BaseChart from './BaseChart.vue';

// Register pie chart
use([PieChart]);

interface DataItem {
    name: string;
    value: number;
}

interface Props {
    data: DataItem[];
    title?: string;
    height?: string;
    loading?: boolean;
    donut?: boolean;
    showLegend?: boolean;
    showLabels?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    height: '400px',
    loading: false,
    donut: false,
    showLegend: true,
    showLabels: true,
});

const option = computed<EChartsOption>(() => {
    const radius = props.donut ? ['40%', '70%'] : '70%';

    const baseOption: EChartsOption = {
        title: props.title
            ? {
                  text: props.title,
                  left: 'center',
              }
            : undefined,
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)',
        },
        legend: props.showLegend
            ? {
                  orient: 'vertical',
                  left: 'left',
              }
            : undefined,
        series: [
            {
                name: props.title || 'Data',
                type: 'pie',
                radius,
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: props.donut ? 10 : 0,
                    borderColor: '#fff',
                    borderWidth: 2,
                },
                label: {
                    show: props.showLabels,
                    formatter: '{b}: {d}%',
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 14,
                        fontWeight: 'bold',
                    },
                },
                labelLine: {
                    show: props.showLabels,
                },
                data: props.data,
            },
        ],
    };

    return baseOption;
});
</script>

<template>
    <BaseChart :option="option" :height="height" :loading="loading" />
</template>
