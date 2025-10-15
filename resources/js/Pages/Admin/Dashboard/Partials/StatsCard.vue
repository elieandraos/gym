<template>
    <div class="bg-white rounded-lg p-6">
        <div v-if="loading" class="animate-pulse">
            <div class="flex items-center gap-4">
                <div v-if="$slots.icon" class="size-12 bg-zinc-100 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-zinc-100 rounded w-24"></div>
                    <div class="h-8 bg-zinc-100 rounded w-16"></div>
                </div>
            </div>
        </div>

        <div v-else :class="$slots.icon ? 'flex items-center gap-4' : 'flex items-center justify-center gap-12 h-full'">
            <div v-if="$slots.icon" class="flex-shrink-0">
                <slot name="icon"></slot>
            </div>
            <div :class="$slots.icon ? 'flex-1' : 'flex flex-col items-start'">
                <p :class="$slots.icon ? 'text-3xl font-[600] text-zinc-900' : 'text-[180px] font-[400] text-zinc-900 leading-[150px] mb-2'">
                    <span v-if="!$slots.icon" ref="counterRef">{{ displayValue }}</span>
                    <span v-else>{{ value }}</span>
                </p>
                <p :class="$slots.icon ? 'text-sm text-zinc-500 mb-1' : 'text-sm text-[#717171]'">{{ title }}</p>
                <p v-if="subtitle" class="text-xs text-zinc-400 mt-1">{{ subtitle }}</p>
            </div>
            <div v-if="change !== undefined && change !== 0 && !$slots.icon" :class="[
                'flex flex-col items-center justify-center gap-1',
                change > 0 ? 'text-green-600' : 'text-red-600'
            ]">
                <svg v-if="change > 0" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <svg v-else class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
                <div class="text-center">
                    <div class="text-lg font-[600]">{{ change > 0 ? '+' : '' }}{{ change }}</div>
                    <div class="text-xs font-[500] opacity-75 whitespace-nowrap">vs last 30 days</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], default: 0 },
    subtitle: { type: String, default: '' },
    loading: { type: Boolean, default: false },
    change: { type: Number, default: undefined },
})

const displayValue = ref(0)
const counterRef = ref(null)

const animateCounter = (target) => {
    const duration = 1500 // 1.5 seconds
    const steps = 60
    const increment = target / steps
    const stepDuration = duration / steps
    let current = 0

    const timer = setInterval(() => {
        current += increment
        if (current >= target) {
            displayValue.value = target
            clearInterval(timer)
        } else {
            displayValue.value = Math.floor(current)
        }
    }, stepDuration)
}

watch(() => props.value, (newValue) => {
    if (!props.loading && newValue > 0) {
        animateCounter(newValue)
    }
}, { immediate: true })

onMounted(() => {
    if (!props.loading && props.value > 0) {
        animateCounter(props.value)
    }
})
</script>
