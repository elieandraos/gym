<template>
    <div ref="container" class="isolate flex flex-auto flex-col bg-white scroll-smooth">
        <div class="flex w-full max-w-full flex-none flex-col">
            <!-- Date header -->
            <div class="sticky top-0 z-30 bg-white shadow-sm">
                <div class="flex">
                    <div class="flex-none w-14 bg-white"></div>
                    <div class="flex-auto">
                        <div class="flex h-14 items-center justify-center border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">{{ date }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid container -->
            <div class="flex flex-auto">
                <!-- time gutter -->
                <TimeGutter />

                <!-- grid, lines & events -->
                <div class="grid flex-auto grid-cols-1 grid-rows-1">
                    <!-- horizontal lines -->
                    <div
                        class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                        style="grid-template-rows: repeat(30, minmax(3.5rem,1fr))"
                    >
                        <div ref="containerOffset" class="row-end-1 h-7"></div>
                        <template v-for="(hour, idx) in hours" :key="idx">
                            <div>
                                <div
                                    class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs text-gray-400"
                                >
                                    {{ format(hour,'ha') }}
                                </div>
                            </div>
                            <div v-if="idx < hours.length - 1"></div>
                        </template>
                    </div>

                    <!-- Single day column -->
                    <div class="col-start-1 col-end-2 row-start-1 grid grid-cols-1">
                        <div class="row-span-full border-r border-gray-100" />
                    </div>

                    <!-- events -->
                    <ol
                        class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 pr-8"
                        style="grid-template-rows: 1.75rem repeat(180, minmax(0,1fr)) auto"
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

defineProps({
    date: {
        type: String,
        default: ''
    },
    hours: {
        type: Array,
        default: () => []
    },
    filteredEvents: {
        type: Array,
        default: () => []
    }
})

defineEmits(['openModal'])

// Template refs
const container = ref(null)
const containerOffset = ref(null)
</script>