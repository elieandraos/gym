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

            <PrimaryButton
                v-if="!isCurrent"
                type="button"
                @click="$emit('currentWeekClick')"
            >
                Jump to Today
            </PrimaryButton>

            <div class="flex items-center space-x-2">
                <slot name="filters"></slot>

                <!-- View switcher -->
                <SelectInput
                    :model-value="'weekly'"
                    :options="[
                        { value: 'daily', name: 'Daily' },
                        { value: 'weekly', name: 'Weekly' }
                    ]"
                    size="auto"
                    @update:model-value="handleViewChange"
                />
            </div>
        </div>

        <!-- Days header row -->
        <div class="flex-none mt-1 bg-white border-y border-gray-100 sm:pr-8">
            <div class="-mr-px hidden grid-cols-6 divide-x divide-gray-100 text-sm text-gray-500 sm:grid">
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
import { router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'

defineProps({
    label: {
        type: String,
        required: true
    },
    headerDays: {
        type: Array,
        default: () => []
    },
    isCurrent: {
        type: Boolean,
        default: false
    }
})

defineEmits(['prevClick', 'nextClick', 'currentWeekClick'])

const handleViewChange = (view) => {
    if (view === 'daily') {
        router.visit('/daily-calendar')
    }
}
</script>
