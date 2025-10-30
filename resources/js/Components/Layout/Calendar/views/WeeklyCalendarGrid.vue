<template>
    <div ref="container" class="isolate flex flex-auto flex-col bg-white overflow-auto scroll-smooth">
        <div class="flex min-w-[800px] flex-none flex-col">
            <!-- grid container -->
            <div class="flex flex-auto min-h-0">
                <!-- time gutter -->
                <TimeGutter />

                <!-- grid, lines & events -->
                <div class="grid flex-auto grid-cols-1 grid-rows-1">
                    <!-- horizontal lines -->
                    <div
                        class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                        :style="{ gridTemplateRows: `repeat(${gridRowCount}, minmax(3.5rem,1fr))` }"
                    >
                        <div ref="containerOffset" class="row-end-1 h-4"></div>
                        <template v-for="(hour, idx) in hours" :key="idx">
                            <div class="relative">
                                <div
                                    class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs text-gray-400"
                                >
                                    {{ format(hour,'ha') }}
                                </div>
                            </div>
                            <div v-if="idx < hours.length - 1" class="relative">
                                <!-- 30-minute marker -->
                                <div class="sticky left-0 z-10 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs text-gray-300">
                                    {{ format(hour,'h') }}:30
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- vertical columns -->
                    <div
                        class="col-start-1 col-end-2 row-start-1 hidden divide-x divide-gray-100 sm:grid"
                        :style="{ gridTemplateColumns: `repeat(${headerDays.length}, minmax(0, 1fr))` }"
                    >
                        <div
                            v-for="n in headerDays.length + 1"
                            :key="n"
                            :class="n < headerDays.length + 1 ? 'row-span-full' : 'row-span-full w-8'"
                        />
                    </div>

                    <!-- events -->
                    <ol
                        class="col-start-1 col-end-2 row-start-1 grid sm:pr-8"
                        :style="{
                            gridTemplateColumns: `repeat(${headerDays.length}, minmax(0, 1fr))`,
                            gridTemplateRows: `1rem repeat(${gridRowCount}, minmax(3.5rem,1fr)) auto`
                        }"
                    >
                        <li
                            v-for="slot in filteredEvents"
                            :key="slot.col + '-' + slot.rowStart + '-' + slot.members.join(',')"
                            class="relative py-px"
                            :style="{
                              gridColumnStart: slot.col,
                              gridRow: slot.rowStart + ' / span ' + slot.span
                            }"
                        >
                            <EventCard
                                :slot="slot"
                                @open-modal="$emit('openModal', slot)"
                            />
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { format } from 'date-fns'

import TimeGutter from '../components/TimeGutter.vue'
import EventCard from '../components/EventCard.vue'
import { useCalendarAutoScroll } from '../composables/useCalendarAutoScroll.js'

const props = defineProps({
    headerDays: {
        type: Array,
        default: () => []
    },
    hours: {
        type: Array,
        default: () => []
    },
    filteredEvents: {
        type: Array,
        default: () => []
    },
    startHour: {
        type: Number,
        default: 6
    },
    endHour: {
        type: Number,
        default: 22
    },
    autoScrollToTime: {
        type: Boolean,
        default: true
    }
})

defineEmits(['openModal'])

// Template refs
const container = ref(null)
const containerOffset = ref(null)

// Calculate grid row count: (hours × 2 for half-hour slots) - 1
// We subtract 1 because the last hour doesn't need a half-hour slot
const gridRowCount = computed(() => {
    const hourCount = props.endHour - props.startHour + 1
    return (hourCount * 2) - 1
})

// Auto-scroll to current time
useCalendarAutoScroll(container, props.startHour, props.endHour, props.autoScrollToTime)
</script>