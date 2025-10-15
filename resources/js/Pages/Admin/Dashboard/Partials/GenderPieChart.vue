<template>
    <div class="bg-white rounded-lg">
        <div v-if="loading" class="animate-pulse">
            <div class="flex items-center justify-center">
                <div class="size-40 bg-zinc-100 rounded-full"></div>
            </div>
        </div>

        <div v-else>
            <div class="flex items-center justify-center py-10">
                <Doughnut :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
    maleCount: { type: Number, default: 0 },
    femaleCount: { type: Number, default: 0 },
    loading: { type: Boolean, default: false },
})

const chartData = computed(() => ({
    labels: ['Male', 'Female'],
    datasets: [
        {
            data: [props.maleCount, props.femaleCount],
            backgroundColor: [
                'rgba(99, 102, 241, 0.8)',
                'rgba(236, 72, 153, 0.8)',
            ],
            borderColor: [
                'rgba(99, 102, 241, 1)',
                'rgba(236, 72, 153, 1)',
            ],
            borderWidth: 0,
        }
    ]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    animation: false,
    elements: {
        arc: {
            hoverOffset: 0,
            borderWidth: 0
        }
    },
    layout: {
        padding: {
            left: 10,
            right: 10,
            top: 10,
            bottom: 10
        }
    },
    plugins: {
        legend: {
            position: 'right',
            align: 'center',
            labels: {
                padding: 20,
                color: '#71717a',
                font: {
                    size: 14,
                    weight: '500',
                    family: 'Inter'
                },
                usePointStyle: true,
                pointStyle: 'circle',
                boxWidth: 14,
                boxHeight: 14
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            cornerRadius: 8,
            titleFont: {
                size: 13,
                weight: '500',
                family: 'Inter'
            },
            bodyFont: {
                size: 12,
                family: 'Inter'
            },
            callbacks: {
                label: function(context) {
                    const label = context.label || ''
                    const value = context.parsed
                    const total = context.dataset.data.reduce((a, b) => a + b, 0)
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0
                    return `${label}: ${value} (${percentage}%)`
                }
            }
        }
    },
    cutout: '65%',
}
</script>

<style scoped>
canvas {
    width: 100% !important;
    height: 100% !important;
    max-width: 502px !important;
    max-height: 350px !important;
}
</style>
