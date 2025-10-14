<template>
    <div ref="chartRef" class="w-full h-full"></div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    color: {
        type: String,
        default: '#3b82f6'
    },
    type: {
        type: String,
        default: 'line', // 'line' or 'bar'
        validator: (value) => ['line', 'bar'].includes(value)
    },
    smooth: {
        type: Boolean,
        default: true
    },
    showArea: {
        type: Boolean,
        default: true
    }
});

const chartRef = ref(null);
let chartInstance = null;

const initChart = () => {
    if (!chartRef.value) return;
    
    // Dispose existing chart instance
    if (chartInstance) {
        chartInstance.dispose();
    }
    
    // Create new chart instance
    chartInstance = echarts.init(chartRef.value);
    
    const option = {
        grid: {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0,
        },
        xAxis: {
            type: 'category',
            show: false,
            data: props.data.map((_, index) => index),
        },
        yAxis: {
            type: 'value',
            show: false,
        },
        series: [
            {
                data: props.data,
                type: props.type,
                smooth: props.smooth,
                symbol: 'none',
                lineStyle: {
                    width: 2,
                    color: props.color,
                },
                itemStyle: {
                    color: props.color,
                },
                areaStyle: props.showArea ? {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {
                            offset: 0,
                            color: `${props.color}40` // 25% opacity
                        },
                        {
                            offset: 1,
                            color: `${props.color}00` // 0% opacity
                        }
                    ])
                } : null,
                animation: true,
                animationDuration: 800,
                animationEasing: 'cubicOut',
            }
        ],
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            borderColor: props.color,
            borderWidth: 1,
            textStyle: {
                color: '#333',
                fontSize: 12,
            },
            formatter: (params) => {
                const value = params[0].value;
                return `<div style="font-weight: 600;">${value.toFixed(2)}</div>`;
            },
            axisPointer: {
                type: 'line',
                lineStyle: {
                    color: props.color,
                    width: 1,
                    type: 'dashed'
                }
            }
        }
    };
    
    chartInstance.setOption(option);
    
    // Handle window resize
    const resizeObserver = new ResizeObserver(() => {
        chartInstance?.resize();
    });
    resizeObserver.observe(chartRef.value);
};

// Watch for data changes
watch(() => props.data, () => {
    nextTick(() => {
        initChart();
    });
}, { deep: true });

watch(() => props.color, () => {
    nextTick(() => {
        initChart();
    });
});

onMounted(() => {
    nextTick(() => {
        initChart();
    });
});

// Cleanup on unmount
import { onBeforeUnmount } from 'vue';
onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.dispose();
    }
});
</script>

<style scoped>
/* Chart container */
</style>