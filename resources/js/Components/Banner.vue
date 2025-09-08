<template>
    <div :class="bannerClasses">
        <div class="flex items-center justify-between">
            <div class="flex items-center min-w-0 flex-1">
                <div :class="textClasses">
                    <slot />
                </div>
            </div>

            <div v-if="showClose" class="shrink-0 ms-3">
                <button
                    type="button"
                    class="-me-1 flex p-2 rounded-md focus:outline-none transition"
                    :class="closeButtonClasses"
                    aria-label="Dismiss"
                    @click="$emit('close')">
                    <svg :class="iconClasses" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    showClose: {
        type: Boolean,
        default: false
    }
})

defineEmits(['close'])

const bannerClasses = computed(() => {
    const baseClasses = 'max-w-screen-xl mx-auto py-3 px-4 rounded-lg'

    switch (props.type) {
        case 'success':
            return `${baseClasses} bg-lime-100 border border-lime-300`
        case 'error':
            return `${baseClasses} bg-red-100 border border-red-300`
        case 'warning':
            return `${baseClasses} bg-yellow-100 border border-yellow-300`
        case 'info':
        default:
            return `${baseClasses} bg-blue-100 border border-blue-300`
    }
})

const textClasses = computed(() => {
    const baseClasses = 'ms-3 font-medium text-sm'

    switch (props.type) {
        case 'success':
            return `${baseClasses} text-lime-800`
        case 'error':
            return `${baseClasses} text-red-800`
        case 'warning':
            return `${baseClasses} text-yellow-800`
        case 'info':
        default:
            return `${baseClasses} text-blue-800`
    }
})

const iconClasses = computed(() => {
    const baseClasses = 'h-5 w-5'

    switch (props.type) {
        case 'success':
            return `${baseClasses} text-lime-600`
        case 'error':
            return `${baseClasses} text-red-600`
        case 'warning':
            return `${baseClasses} text-yellow-600`
        case 'info':
        default:
            return `${baseClasses} text-blue-600`
    }
})

const closeButtonClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'hover:bg-lime-200 focus:bg-lime-200'
        case 'error':
            return 'hover:bg-red-200 focus:bg-red-200'
        case 'warning':
            return 'hover:bg-yellow-200 focus:bg-yellow-200'
        case 'info':
        default:
            return 'hover:bg-blue-200 focus:bg-blue-200'
    }
})
</script>
