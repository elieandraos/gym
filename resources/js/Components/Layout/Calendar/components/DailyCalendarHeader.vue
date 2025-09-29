<template>
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100">
        <!-- Navigation and filters row -->
        <div class="flex items-center justify-between py-2 pb-5">
            <div class="flex items-center space-x-2">
                <button
                    @click="$emit('prevClick')"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer"
                >
                    <ChevronLeftIcon class="size-6" aria-hidden="true" />
                </button>
                <button
                    @click="$emit('nextClick')"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer"
                >
                    <ChevronRightIcon class="size-6" aria-hidden="true" />
                </button>
                <span class="text-gray-200 uppercase text-xl font-medium">
                    {{ dateLabel }}
                </span>
            </div>

            <slot name="filters"></slot>
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