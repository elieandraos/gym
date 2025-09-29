<template>
    <div class="flex h-full flex-col">
        <!-- week nav: sticks to top -->
        <header
            class="sticky top-0 z-50 flex items-center justify-between border-b border-gray-200 bg-white pb-2 pt-4 -mt-6"
        >
            <div class="flex items-center space-x-2">
                <button
                    @click="prevWeek"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer"
                >
                    <ChevronLeftIcon class="size-6" aria-hidden="true" />
                </button>
                <button
                    @click="nextWeek"
                    class="text-gray-500 hover:text-gray-700 cursor-pointer"
                >
                    <ChevronRightIcon class="size-6" aria-hidden="true" />
                </button>
                <span class="text-gray-200 uppercase text-xl font-medium">
                    {{ monthLabel }}
                </span>
            </div>

            <!-- trainer filter checkboxes -->
            <div class="flex items-center space-x-4">
                <template v-for="trainer in availableTrainers" :key="trainer.id">
                    <label class="inline-flex items-center text-sm cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="selectedTrainers"
                            :value="trainer.id"
                            @change="applyTrainerFilter"
                            class="h-4 w-4 rounded border-gray-300 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="ml-2 text-gray-700 cursor-pointer">{{ trainer.first_name }}</span>
                    </label>
                </template>
            </div>
        </header>

        <div ref="container" class="isolate flex flex-auto flex-col bg-white">
            <div class="flex w-[165%] max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
                <!-- days header -->
                <div
                    ref="containerNav"
                    class="sticky top-[50px] z-40 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8"
                >
                    <div
                        class="-mr-px hidden grid-cols-6 divide-x divide-gray-100 border-r border-gray-100 text-sm text-gray-500 sm:grid"
                    >
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

                <!-- horizontal scroll only -->
                <div class="flex flex-auto overflow-x-auto">
                    <!-- time gutter -->
                    <div
                        class="sticky left-0 z-20 w-14 flex-none bg-white ring-1 ring-gray-100"
                    ></div>

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

                        <!-- vertical columns -->
                        <div
                            class="col-start-1 col-end-2 row-start-1 hidden grid-cols-6 divide-x divide-gray-100 sm:grid sm:grid-cols-6"
                        >
                            <div
                                v-for="n in 7"
                                :key="n"
                                :class="n < 7 ? 'row-span-full' : 'row-span-full w-8'"
                            />
                        </div>

                        <!-- events -->
                        <ol
                            class="col-start-1 col-end-2 row-start-1 grid sm:grid-cols-6 sm:pr-8"
                            style="grid-template-rows: 1.75rem repeat(180, minmax(0,1fr)) auto"
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
                                <component
                                    :is="slot.members.length === 1 ? 'a' : 'div'"
                                    v-bind="slot.members.length === 1
                                        ? { href: slot.url }
                                        : {}"
                                    @click="slot.members.length > 1 ? openMembersPopup(slot) : null"
                                    class="group absolute inset-y-2 flex flex-col overflow-y-auto rounded-lg p-2 text-xs hover:opacity-90 cursor-pointer"
                                    :class="slot.bgClass"
                                    :style="{
                                        left: slot.overlapCount > 1
                                          ? 'calc(' + slot.overlapIndex + '*(100%/' + slot.overlapCount + ') + 0.25rem)'
                                          : '0.25rem',
                                        width: slot.overlapCount > 1
                                          ? 'calc((100%/' + slot.overlapCount + ') - 0.5rem)'
                                          : 'calc(100% - 0.5rem)',
                                        zIndex: slot.overlapCount - slot.overlapIndex
                                      }"
                                >
                                    <p
                                        class="text-xs"
                                        :class="slot.textClass + (slot.members.length === 1 ? ' group-hover:' + slot.hoverText : '')"
                                    >
                                        <time :datetime="slot.start_time">{{ slot.short_time }}</time>
                                    </p>
                                    <p class="font-semibold mt-2" :class="slot.textClass">
                                        {{ slot.members.join(', ') }}
                                    </p>
                                </component>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members popup modal -->
        <div
            v-if="showMembersPopup"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="closeMembersPopup"
        >
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    @click.stop
                >
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    Session Members
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-4">
                                        <time :datetime="selectedSlot?.start_time">{{ selectedSlot?.short_time }}</time>
                                        - {{ selectedSlot?.trainer }}
                                    </p>
                                    <div class="space-y-2">
                                        <button
                                            v-for="member in selectedSlot?.members"
                                            :key="member"
                                            @click="goToBookingSlot(member)"
                                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors"
                                        >
                                            <span class="font-medium text-gray-900">{{ member }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button
                            type="button"
                            @click="closeMembersPopup"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

import {
    addDays, parseISO, format, isSameDay,
    startOfDay, setHours, setMinutes,
    differenceInCalendarDays, differenceInMinutes
} from 'date-fns'

const props = defineProps({
    events: Array,
    is_current: Boolean,
    available_trainers: Array,
    filters: { type: Object, default: () => ({}) }
})
const { route } = window

// state
const today            = new Date()
const selectedTrainers = ref(props.filters?.trainers || [])
const showMembersPopup = ref(false)
const selectedSlot = ref(null)

// navigation
const getNavParams = (start, end) => {
    const params = {
        start: format(start, 'yyyy-MM-dd'),
        end: format(end, 'yyyy-MM-dd')
    }

    if (selectedTrainers.value.length > 0) {
        params.trainers = selectedTrainers.value.join(',')
    }

    return params
}

const prevWeek = () => {
    if (!props.filters?.start) return
    const currentStart = parseISO(props.filters.start)
    const newStart = addDays(currentStart, -7)
    const newEnd = addDays(newStart, 5)

    router.get(route('admin.weekly-calendar.index'), getNavParams(newStart, newEnd))
}

const nextWeek = () => {
    if (!props.filters?.start) return
    const currentStart = parseISO(props.filters.start)
    const newStart = addDays(currentStart, 7)
    const newEnd = addDays(newStart, 5)

    router.get(route('admin.weekly-calendar.index'), getNavParams(newStart, newEnd))
}

// popup methods
const openMembersPopup = (slot) => {
    selectedSlot.value = slot
    showMembersPopup.value = true
}

const closeMembersPopup = () => {
    showMembersPopup.value = false
    selectedSlot.value = null
}

const goToBookingSlot = (memberName) => {
    // Find the event for this specific member and use its direct URL
    const event = props.events.find(e =>
        e.meta_data.member === memberName &&
        e.meta_data.trainer === selectedSlot.value.trainer &&
        e.start_time === selectedSlot.value.start_time
    )

    if (event) {
        window.location.href = event.url
    }
}

// available trainers
const availableTrainers = computed(() => props.available_trainers || [])

// color scheme utility
const getColorScheme = (bgColor) => {
    const colorMap = {
        'bg-blue-50': { text: 'text-blue-700', hover: 'text-blue-700', hoverBg: 'hover:bg-blue-100' },
        'bg-orange-100': { text: 'text-orange-700', hover: 'text-orange-700', hoverBg: 'hover:bg-orange-200' },
        'bg-pink-50': { text: 'text-pink-700', hover: 'text-pink-700', hoverBg: 'hover:bg-pink-100' },
        'bg-emerald-50': { text: 'text-emerald-700', hover: 'text-emerald-700', hoverBg: 'hover:bg-emerald-100' },
        'bg-gray-100': { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' },
        'bg-purple-100': { text: 'text-purple-700', hover: 'text-purple-700', hoverBg: 'hover:bg-purple-200' },
        'bg-cyan-100': { text: 'text-cyan-700', hover: 'text-cyan-700', hoverBg: 'hover:bg-teal-200' },
        'bg-yellow-100': { text: 'text-yellow-700', hover: 'text-yellow-700', hoverBg: 'hover:bg-yellow-200' },
    }
    return colorMap[bgColor] || { text: 'text-gray-700', hover: 'text-gray-700', hoverBg: 'hover:bg-gray-200' }
}

// month–day range label
const monthLabel = computed(() => {
    if (!props.filters?.start || !props.filters?.end) return ''
    const s = parseISO(props.filters.start)
    const e = parseISO(props.filters.end)
    const startFmt = format(s, 'MMM d')
    const endFmt = format(e, 'MMM d')
    return `${startFmt} - ${endFmt}`
})

// apply trainer filter
const applyTrainerFilter = () => {
    if (!props.filters?.start || !props.filters?.end) return
    const currentStart = parseISO(props.filters.start)
    const currentEnd = parseISO(props.filters.end)

    router.get(route('admin.weekly-calendar.index'), getNavParams(currentStart, currentEnd), {
        preserveState: true,
        preserveScroll: true
    })
}

// header days
const headerDays = computed(() => {
    if (!props.filters?.start) return []
    const start = parseISO(props.filters.start)
    return Array.from({ length: 6 }).map((_, i) => {
        const d = addDays(start, i)
        return { short: format(d,'EEE'), day: format(d,'d'), isToday: isSameDay(d, today) }
    })
})

// times gutter
const hours = computed(() =>
    Array.from({ length: 16 }).map((_, i) =>
        setMinutes(setHours(startOfDay(new Date()), 7 + i), 0)
    )
)

// 1) raw + merge same‐trainer+slot
const rawMerged = computed(() => {
    if (!props.filters?.start || !props.events) return []
    const weekStart = parseISO(props.filters.start)
    const slots = props.events.map(event => {
        const start  = parseISO(event.start_time)
        const end    = parseISO(event.end_time)
        const mins   = differenceInMinutes(start, startOfDay(start))
        const dayIx  = differenceInCalendarDays(start, weekStart)
        if (mins < 420 || mins >= 1320 || dayIx < 0 || dayIx > 5) return null

        const col       = dayIx + 1
        const rowStart  = Math.floor((mins - 420) / 5) + 2
        const span      = Math.max(1, Math.ceil(differenceInMinutes(end, start) / 5))
        const colorScheme = getColorScheme(event.meta_data.trainer_color)
        const pal = {
            bg: `${event.meta_data.trainer_color} ${colorScheme.hoverBg}`,
            text: colorScheme.text,
            hover: colorScheme.hover
        }

        return {
            id:         event.id,
            url:        event.url,
            trainer:    event.meta_data.trainer,
            start_time: event.start_time,
            short_time: event.meta_data.short_time,
            col, rowStart, span,
            bgClass:    pal.bg,
            textClass:  pal.text,
            hoverText:  pal.hover,
            member:     event.meta_data.member
        }
    }).filter(Boolean)

    // merge identical
    const map = {}
    slots.forEach(e => {
        const key = `${e.trainer}::${e.col}-${e.rowStart}-${e.span}`
        if (!map[key]) map[key] = { ...e, members: [e.member] }
        else map[key].members.push(e.member)
    })
    return Object.values(map)
})

// 2) events are already filtered on backend, no need for frontend filtering
const filteredRaw = computed(() => rawMerged.value)

// 3) cluster and position
const filteredEvents = computed(() => {
    const byDay = filteredRaw.value.reduce((acc,e) => {
        ;(acc[e.col] ||= []).push(e)
        return acc
    }, {})

    const positioned = []

    Object.values(byDay).forEach(slots => {
        const n = slots.length
        const parent = slots.map((_,i)=>i)
        const find   = i => parent[i]===i?i:(parent[i]=find(parent[i]))
        const union  = (a,b)=>{const ra=find(a), rb=find(b); if(ra!==rb) parent[rb]=ra}

        for (let i=0; i<n; i++)
            for (let j=i+1; j<n; j++){
                const a=slots[i], b=slots[j]
                if (a.rowStart < b.rowStart + b.span && b.rowStart < a.rowStart + a.span)
                    union(i,j)
            }

        const clusters = {}

        for (let i=0; i<n; i++){
            const r = find(i)
            ;(clusters[r] ||= []).push(slots[i])
        }

        Object.values(clusters).forEach(group => {
            group.sort((a,b)=>a.rowStart-b.rowStart||a.id-b.id)
            group.forEach((slot,idx) => {
                slot.overlapCount = group.length
                slot.overlapIndex = idx
                positioned.push(slot)
            })
        })
    })

    return positioned
})
</script>
