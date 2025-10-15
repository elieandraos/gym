<template>
    <div class="bg-white rounded-lg">
        <div v-if="loading" class="animate-pulse">
            <div class="space-y-3">
                <div v-for="i in 3" :key="i" class="h-8 bg-zinc-100 rounded"></div>
            </div>
        </div>

        <div v-else>
            <Bar :data="chartData" :options="chartOptions" :plugins="[profileImagePlugin]" />
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

// Store loaded images
const trainerImages = {}

onMounted(() => {
    // Preload trainer images
    props.trainers.forEach(trainer => {
        const img = new Image()
        img.src = trainer.profile_photo_url
        trainerImages[trainer.id] = img
    })
})

const props = defineProps({
    trainers: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})

const getFirstName = (fullName) => {
    return fullName.split(' ')[0]
}

const tailwindToHex = (tailwindClass) => {
    const colorMap = {
        'bg-blue-50': '#eff6ff',
        'bg-blue-100': '#dbeafe',
        'bg-blue-200': '#bfdbfe',
        'bg-blue-300': '#93c5fd',
        'bg-blue-400': '#60a5fa',
        'bg-blue-500': '#3b82f6',
        'bg-blue-600': '#2563eb',
        'bg-amber-50': '#fffbeb',
        'bg-amber-100': '#fef3c7',
        'bg-amber-200': '#fde68a',
        'bg-amber-300': '#fcd34d',
        'bg-amber-400': '#fbbf24',
        'bg-amber-500': '#f59e0b',
        'bg-pink-50': '#fdf2f8',
        'bg-pink-100': '#fce7f3',
        'bg-pink-200': '#fbcfe8',
        'bg-pink-300': '#f9a8d4',
        'bg-pink-400': '#f472b6',
        'bg-pink-500': '#ec4899',
        'bg-red-50': '#fef2f2',
        'bg-red-100': '#fee2e2',
        'bg-green-50': '#f0fdf4',
        'bg-green-100': '#dcfce7',
        'bg-purple-50': '#faf5ff',
        'bg-purple-100': '#f3e8ff',
        'bg-indigo-50': '#eef2ff',
        'bg-indigo-100': '#e0e7ff',
    }
    return colorMap[tailwindClass] || '#6366f1'
}

const chartData = computed(() => ({
    labels: props.trainers.map(t => t),
    datasets: [
        {
            label: 'Members',
            data: props.trainers.map(t => t.active_members_count),
            backgroundColor: props.trainers.map(t => tailwindToHex(t.color)),
            borderColor: props.trainers.map(t => tailwindToHex(t.color)),
            borderWidth: 0,
            borderRadius: 6,
        }
    ]
}))

// Custom plugin to draw profile pictures
const profileImagePlugin = {
    id: 'profileImages',
    afterDatasetsDraw(chart) {
        const { ctx, scales: { y } } = chart

        chart.data.labels.forEach((trainer, index) => {
            const img = trainerImages[trainer.id]

            if (img && img.complete) {
                const yPos = y.getPixelForTick(index)
                const size = 32
                const xPos = y.left - size - 8

                // Calculate aspect ratio to cover the circle (like object-cover)
                const imgAspect = img.width / img.height
                let sourceWidth, sourceHeight, sourceX, sourceY

                if (imgAspect > 1) {
                    // Image is wider than tall
                    sourceHeight = img.height
                    sourceWidth = img.height
                    sourceX = (img.width - sourceWidth) / 2
                    sourceY = 0
                } else {
                    // Image is taller than wide
                    sourceWidth = img.width
                    sourceHeight = img.width
                    sourceX = 0
                    sourceY = (img.height - sourceHeight) / 2
                }

                ctx.save()
                ctx.beginPath()
                ctx.arc(xPos + size/2, yPos, size/2, 0, Math.PI * 2)
                ctx.closePath()
                ctx.clip()
                ctx.drawImage(
                    img,
                    sourceX, sourceY, sourceWidth, sourceHeight,
                    xPos, yPos - size/2, size, size
                )
                ctx.restore()
            }
        })
    }
}

const chartOptions = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 40
        }
    },
    plugins: {
        profileImages: true,
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            cornerRadius: 8,
            boxPadding: 6,
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
                    const count = context.parsed.x
                    return `  ${count} ${count === 1 ? 'member' : 'members'}`
                },
                title: function(context) {
                    const trainer = context[0].chart.data.labels[context[0].dataIndex]
                    return trainer.name
                }
            }
        }
    },
    scales: {
        x: {
            beginAtZero: true,
            ticks: {
                precision: 0,
                color: '#71717a',
                font: {
                    size: 11,
                    family: 'Inter'
                }
            },
            grid: {
                color: '#f4f4f5',
                drawBorder: false
            },
            border: {
                display: false
            }
        },
        y: {
            ticks: {
                display: false
            },
            grid: {
                display: false,
                drawBorder: false
            },
            border: {
                display: false
            }
        }
    }
}
</script>

<style scoped>
canvas {
    height: 240px !important;
}
</style>
