<template>
    <div class="flex h-full flex-col">

        <header class="flex flex-none items-center justify-between border-b border-gray-200 px-6 py-4">
            <div class="flex items-center">
                <div class="relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
                    <button
                        @click="prevWeek"
                        :disabled="currentWeekIndex === 0"
                        class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 cursor-pointer hover:text-gray-500 focus:relative disabled:opacity-30 md:w-9 md:pr-0 md:hover:bg-gray-50"
                    >
                        <ChevronLeftIcon class="size-5" aria-hidden="true" />
                    </button>
                    <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden" />
                    <button
                        @click="nextWeek"
                        :disabled="currentWeekIndex === weeks.length - 1"
                        class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 cursor-pointer hover:text-gray-500 focus:relative disabled:opacity-30 md:w-9 md:pl-0 md:hover:bg-gray-50"
                    >
                        <ChevronRightIcon class="size-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>

        <div ref="container" class="isolate flex flex-auto flex-col overflow-auto bg-white">
            <div class="flex w-[165%] max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
                <div ref="containerNav" class="sticky top-0 z-30 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8">
                    <!-- header days -->
                    <div class="-mr-px hidden grid-cols-6 divide-x divide-gray-100 border-r border-gray-100 text-sm/6 text-gray-500 sm:grid">
                        <div class="col-end-1 w-14" />
                        <template v-for="(day, key) in headerDays" :key="key">
                            <div class="flex items-center justify-center py-3">
                                <span>{{ day.short }}
                                    <span class="items-center justify-center font-semibold" :class="day.isToday ? 'p-1 rounded-full bg-indigo-600 text-white' : 'text-gray-900'">{{ day.day }}</span>
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex flex-auto">
                    <div class="sticky left-0 z-10 w-14 flex-none bg-white ring-1 ring-gray-100" />
                    <div class="grid flex-auto grid-cols-1 grid-rows-1">
                        <!-- Horizontal lines: 30 rows = 15 hours (7 AM → 10 PM) at ½-hour increments. -->
                        <div
                            class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                            style="grid-template-rows: repeat(30, minmax(3.5rem, 1fr))"
                        >
                            <div ref="containerOffset" class="row-end-1 h-7" />
                            <template v-for="(hour, idx) in hours" :key="idx">
                                <div>
                                    <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">
                                        {{ format(hour, 'ha') }}
                                    </div>
                                </div>
                                <div v-if="idx < hours.length - 1" />
                            </template>
                        </div>

                        <!-- Vertical lines -->
                        <div class="col-start-1 col-end-2 row-start-1 hidden grid-cols-6 grid-rows-1 divide-x divide-gray-100 sm:grid sm:grid-cols-6">
                            <div class="col-start-1 row-span-full" />
                            <div class="col-start-2 row-span-full" />
                            <div class="col-start-3 row-span-full" />
                            <div class="col-start-4 row-span-full" />
                            <div class="col-start-5 row-span-full" />
                            <div class="col-start-6 row-span-full" />
                            <div class="col-start-7 row-span-full w-8" />
                        </div>

                        <!-- Events -->
                        <ol class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-6 sm:pr-8" style="grid-template-rows: 1.75rem repeat(180, minmax(0, 1fr)) auto">
                            <li class="relative py-px flex sm:col-start-3" style="grid-row: 2 / span 6">
                                <a href="#" class="group absolute inset-x-1 inset-y-2 flex flex-col overflow-y-auto rounded-lg bg-blue-50 p-2 text-xs/5 hover:bg-blue-100">
                                    <p class="order-1 font-semibold text-blue-700">Breakfast</p>
                                    <p class="text-blue-500 group-hover:text-blue-700">
                                        <time datetime="2022-01-12T06:00">7:00 AM</time>
                                    </p>
                                </a>
                            </li>
                            <li class="relative py-px flex sm:col-start-3" style="grid-row: 8 / span 30">
                                <a href="#" class="group absolute inset-x-1 inset-y-2 flex flex-col overflow-y-auto rounded-lg bg-pink-50 p-2 text-xs/5 hover:bg-pink-100">
                                    <p class="order-1 font-semibold text-pink-700">Flight to Paris</p>
                                    <p class="text-pink-500 group-hover:text-pink-700">
                                        <time datetime="2022-01-12T07:30">7:30 AM</time>
                                    </p>
                                </a>
                            </li>
                            <li class="relative py-px hidden sm:col-start-6 sm:flex" style="grid-row: 38 / span 24">
                                <a href="#" class="group absolute inset-x-1 inset-y-2 flex flex-col overflow-y-auto rounded-lg bg-gray-100 p-2 text-xs/5 hover:bg-gray-200">
                                    <p class="order-1 font-semibold text-gray-700">Meeting with design team at Disney</p>
                                    <p class="text-gray-500 group-hover:text-gray-700">
                                        <time datetime="2022-01-15T10:00">10:00 AM</time>
                                    </p>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { addDays, parseISO, format, isSameDay, startOfDay, setHours, setMinutes } from 'date-fns'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    weeks: { type: Array, required: true }
})

const container = ref(null)
const containerNav = ref(null)
const containerOffset = ref(null)

// Track current week
const currentWeekIndex = ref(props.weeks.findIndex(w => w.is_current))
const weeks = props.weeks
const today = new Date()

// Navigation
const prevWeek = () => currentWeekIndex.value > 0 && currentWeekIndex.value--
const nextWeek = () => currentWeekIndex.value < weeks.length - 1 && currentWeekIndex.value++

// Computed selected week
const selectedWeek = computed(() => weeks[currentWeekIndex.value])

// Days calendar header
const headerDays = computed(() => {
    const start = parseISO(selectedWeek.value.start)
    return Array.from({ length: 6 }).map((_, i) => {
        const date = addDays(start, i)
        return {
            letter: format(date, 'EEEEE'),
            short: format(date, 'EEE'),
            day: format(date, 'd'),
            isToday: isSameDay(date, today)
        }
    })
})

// Time slots from 7 AM to 10 PM (16 hours → 32 half-hour rows)
const hours = computed(() =>
    Array.from({ length: 16 }).map((_, i) =>
        setMinutes(setHours(startOfDay(new Date()), 7 + i), 0)
    )
)
</script>
