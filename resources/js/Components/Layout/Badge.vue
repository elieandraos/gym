<template>
    <span
        class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs lowercase font-medium sm:text-xs/5 forced-colors:outline"
        :class="classList">
        <slot></slot>
    </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: { type: String, required: false, default: 'success' },
})

const classList = computed(() => {
    let str

    switch (props.type) {
    case 'error':
        str = 'bg-red-400/20 text-red-700'
        break
    case 'warning':
        str = 'bg-amber-400/20 text-amber-700'
        break
    case 'info':
            str = 'bg-indigo-400/20 text-indigo-700'
            break
    case 'success': default:
        str = 'bg-lime-400/20 text-lime-700'
        break
    }

    return str
})
</script>
