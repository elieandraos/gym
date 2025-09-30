<template>
    <div ref="container" class="isolate flex flex-auto flex-col bg-white overflow-auto scroll-smooth">
        <div class="flex w-full flex-col">
            <!-- Grid container -->
            <div class="flex" style="min-height: 2000px;">
                <!-- time gutter -->
                <TimeGutter />

                <!-- grid, lines & events -->
                <div class="grid flex-auto grid-cols-1 grid-rows-1">
                    <!-- horizontal lines -->
                    <div
                        class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                        style="grid-template-rows: repeat(34, minmax(3.5rem,1fr))"
                    >
                        <div ref="containerOffset" class="row-end-1 h-7"></div>
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

                    <!-- Single day column -->
                    <div class="col-start-1 col-end-2 row-start-1 grid grid-cols-1">
                        <div class="row-span-full border-r border-gray-100" />
                    </div>

                    <!-- events -->
                    <ol
                        class="col-start-1 col-end-2 row-start-1 grid grid-cols-1"
                        style="grid-template-rows: 1.75rem repeat(34, minmax(3.5rem,1fr)) auto"
                    >
                        <li
                            v-for="slot in filteredEvents"
                            :key="slot.id + '-' + slot.rowStart"
                            class="relative py-px"
                            :style="{
                              gridColumnStart: 1,
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
import { ref } from 'vue'
import { format } from 'date-fns'

import TimeGutter from '../components/TimeGutter.vue'
import EventCard from '../components/EventCard.vue'
import { useCalendarAutoScroll } from '../composables/useCalendarAutoScroll.js'

const props = defineProps({
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

// Auto-scroll to current time
useCalendarAutoScroll(container, props.startHour, props.endHour, props.autoScrollToTime)
</script>