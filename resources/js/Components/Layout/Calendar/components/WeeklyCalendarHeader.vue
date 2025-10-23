<template>
    <header class="sticky top-0 z-50 bg-white pt-4">
        <!-- Navigation and filters row -->
        <div class="flex items-center justify-between py-2">
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

            <!-- View switcher -->
            <a href="/daily-calendar" class="text-sm text-sky-500 hover:text-sky-700 font-[500]">switch to day view</a>

            <slot name="filters"></slot>
        </div>

        <!-- Days header row -->
        <div class="flex-none mt-1 bg-white shadow ring-1 ring-black/5 sm:pr-8">
            <div class="-mr-px hidden grid-cols-6 divide-x divide-gray-100 border-r border-gray-100 text-sm text-gray-500 sm:grid">
                <div class="col-end-1 w-14"></div>
                <template v-for="day in headerDays" :key="day.day">
                    <div class="flex items-center justify-center py-3">
                        <span class="text-gray-900">
                            {{ day.short }}
                            <span
                                class="ml-1 inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                :class="day.isToday ? 'bg-black text-white' : 'text-gray-700'"
                            >
                                {{ day.day }}
                            </span>
                        </span>
                    </div>
                </template>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

defineProps({
    label: {
        type: String,
        required: true
    },
    headerDays: {
        type: Array,
        default: () => []
    }
})

defineEmits(['prevClick', 'nextClick'])
</script>
