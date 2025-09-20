<template>
    <div class="flex gap-3">
        <div
            v-for="(color, index) in colorOptions"
            :key="index"
            @click="selectColor(color)"
            class="w-10 h-10 rounded-lg cursor-pointer transition-all hover:scale-105 border-1"
            :class="[
                getColorClass(color),
                isSelected(color) ? 'border-gray-800' : 'border-transparent'
            ]"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: String,
    colors: {
        type: Array,
        default: () => []
    },
    mode: {
        type: String,
        default: 'tailwind',
        validator: (value) => ['tailwind', 'hex'].includes(value)
    }
})

const emit = defineEmits(['update:modelValue'])

const defaultTailwindColors = [
    'bg-blue-50',
    'bg-orange-100',
    'bg-pink-50',
    'bg-emerald-50',
    'bg-purple-100',
    'bg-yellow-100'
]

const colorOptions = computed(() => {
    return props.colors.length > 0 ? props.colors : defaultTailwindColors
})

const selectColor = (color) => {
    emit('update:modelValue', color)
}

const isSelected = (color) => {
    return props.modelValue === color
}

const getColorClass = (color) => {
    if (props.mode === 'hex') {
        return { backgroundColor: color }
    }
    return color
}
</script>
