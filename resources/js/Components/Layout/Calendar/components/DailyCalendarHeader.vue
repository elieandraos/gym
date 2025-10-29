<template>
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100 pt-4">
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

            <PrimaryButton
                v-if="!isToday"
                type="button"
                @click="$emit('todayClick')"
            >
                Jump to Today
            </PrimaryButton>

            <div class="flex items-center space-x-2">
                <slot name="filters"></slot>

                <!-- View switcher -->
                <SelectInput
                    :model-value="'daily'"
                    :options="[
                        { value: 'daily', name: 'Daily' },
                        { value: 'weekly', name: 'Weekly' }
                    ]"
                    size="auto"
                    @update:model-value="handleViewChange"
                />
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
    dateLabel: {
        type: String,
        required: true
    },
    isToday: {
        type: Boolean,
        default: false
    }
})

defineEmits(['prevClick', 'nextClick', 'todayClick'])

const handleViewChange = (view) => {
    if (view === 'weekly') {
        router.visit('/weekly-calendar')
    }
}
</script>
