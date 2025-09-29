<template>
    <header
        class="sticky top-0 z-50 flex items-center justify-between border-b border-gray-200 bg-white pb-2 pt-4 -mt-6"
    >
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
                {{ label }}
            </span>
        </div>

        <slot name="filters"></slot>
    </header>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

defineProps({
    label: {
        type: String,
        required: true
    }
})

defineEmits(['prevClick', 'nextClick'])
</script>