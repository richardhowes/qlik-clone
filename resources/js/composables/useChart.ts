import type { EChartsOption } from 'echarts';
import { computed } from 'vue';
import { useAppearance } from './useAppearance';

export function useChart() {
    const { appearance } = useAppearance();

    const isDark = computed(() => appearance.value === 'dark');

    const getTheme = () => {
        return isDark.value ? 'dark' : '';
    };

    const getBaseOptions = (): EChartsOption => {
        return {
            backgroundColor: 'transparent',
            textStyle: {
                color: isDark.value ? '#f3f4f6' : '#111827',
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: isDark.value ? '#1f2937' : '#ffffff',
                borderColor: isDark.value ? '#374151' : '#e5e7eb',
                textStyle: {
                    color: isDark.value ? '#f3f4f6' : '#111827',
                },
            },
            legend: {
                textStyle: {
                    color: isDark.value ? '#f3f4f6' : '#111827',
                },
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true,
            },
        };
    };

    const mergeOptions = (options: EChartsOption): EChartsOption => {
        const baseOptions = getBaseOptions();
        return {
            ...baseOptions,
            ...options,
            textStyle: {
                ...baseOptions.textStyle,
                ...(options.textStyle || {}),
            },
            tooltip: {
                ...baseOptions.tooltip,
                ...(options.tooltip || {}),
            },
            legend: {
                ...baseOptions.legend,
                ...(options.legend || {}),
            },
            grid: {
                ...baseOptions.grid,
                ...(options.grid || {}),
            },
        };
    };

    return {
        isDark,
        getTheme,
        getBaseOptions,
        mergeOptions,
    };
}
