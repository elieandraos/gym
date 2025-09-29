<template>
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-8 py-3">
            <div class="flex items-center justify-between max-w-7xl">
                <div class="flex items-center space-x-2">
                    <button
                        @click="$emit('prevClick')"
                        class="text-gray-500 hover:text-gray-700 cursor-pointer"
                    >
                        <ChevronLeftIcon class="size-5" aria-hidden="true" />
                    </button>
                    <button
                        @click="$emit('nextClick')"
                        class="text-gray-500 hover:text-gray-700 cursor-pointer"
                    >
                        <ChevronRightIcon class="size-5" aria-hidden="true" />
                    </button>
                    <span class="text-gray-900 text-lg font-medium ml-4">
                        {{ dateLabel }}
                    </span>
                </div>

                <slot name="filters"></slot>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

defineProps({
    dateLabel: {
        type: String,
        required: true
    }
})

defineEmits(['prevClick', 'nextClick'])
</script>