<script setup lang="ts">
import VChart from 'vue-echarts';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    DatasetComponent,
    TransformComponent,
} from 'echarts/components';
import type { EChartsOption } from 'echarts';
import { computed, ref, watch } from 'vue';
import { useChart } from '@/composables/useChart';

// Register ECharts components
use([
    CanvasRenderer,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    DatasetComponent,
    TransformComponent,
]);

interface Props {
    option: EChartsOption;
    height?: string;
    width?: string;
    loading?: boolean;
    autoresize?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    height: '400px',
    width: '100%',
    loading: false,
    autoresize: true,
});

const { getTheme, mergeOptions } = useChart();

const chartRef = ref<InstanceType<typeof VChart>>();

const computedOption = computed(() => {
    return mergeOptions(props.option);
});

const theme = computed(() => getTheme());

// Export chart as image
const exportAsImage = (filename = 'chart.png') => {
    if (chartRef.value) {
        const url = chartRef.value.getDataURL({
            type: 'png',
            pixelRatio: 2,
            backgroundColor: '#fff',
        });
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
    }
};

// Refresh chart
const refresh = () => {
    if (chartRef.value) {
        chartRef.value.refresh();
    }
};

defineExpose({
    exportAsImage,
    refresh,
});
</script>

<template>
    <div class="relative">
        <v-chart
            ref="chartRef"
            :option="computedOption"
            :theme="theme"
            :style="{ height, width }"
            :loading="loading"
            :autoresize="autoresize"
            class="chart"
        />
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-background/50">
            <div class="text-muted-foreground">Loading...</div>
        </div>
    </div>
</template>

<style scoped>
.chart {
    transition: all 0.3s ease;
}</style>