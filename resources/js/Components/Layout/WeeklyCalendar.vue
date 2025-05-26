<template>
    <div class="flex h-full flex-col">
        <!-- week nav: sticks to top -->
        <header
            class="sticky top-0 z-50 flex flex-none items-center justify-between border-b border-gray-200 bg-white py-4 px-6"
        >
            <div class="flex items-center space-x-3">
                <div class="relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
                    <button
                        @click="prevWeek"
                        :disabled="currentWeekIndex === 0"
                        class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 disabled:opacity-30 cursor-pointer"
                    >
                        <ChevronLeftIcon class="size-5" aria-hidden="true" />
                    </button>
                    <button
                        @click="nextWeek"
                        :disabled="currentWeekIndex === weeks.length - 1"
                        class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 disabled:opacity-30 cursor-pointer"
                    >
                        <ChevronRightIcon class="size-5" aria-hidden="true" />
                    </button>
                </div>
                <!-- month–year label -->
                <span class="text-gray-200 uppercase text-xl">
                  {{ monthLabel }}
                </span>
            </div>

            <!-- trainer filter checkboxes -->
            <div class="flex items-center space-x-4">
                <template v-for="trainer in trainers" :key="trainer">
                    <label class="inline-flex items-center text-sm cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="selectedTrainers"
                            :value="trainer"
                            class="h-4 w-4 rounded border-gray-300 bg-white text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                        />
                        <span class="ml-2 text-gray-700 cursor-pointer">{{ trainer }}</span>
                    </label>
                </template>
            </div>
        </header>

        <div ref="container" class="isolate flex flex-auto flex-col bg-white">
            <div class="flex w-[165%] max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
                <!-- days header -->
                <div
                    ref="containerNav"
                    class="sticky top-16 z-40 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8"
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
                                        :class="day.isToday ? 'bg-indigo-600 text-white' : 'text-gray-700'"
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
                            <div v-for="n in 7" :key="n" :class="n<7 ? 'row-span-full' : 'row-span-full w-8'" />
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
                                      ? { href: route('admin.bookings-slots.show', slot.id) }
                                      : {}"
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
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import {
    addDays, parseISO, format, isSameDay,
    startOfDay, setHours, setMinutes,
    differenceInCalendarDays, differenceInMinutes
} from 'date-fns'

const props = defineProps({ weeks: Array })
const { route } = window

// state
const currentWeekIndex = ref(props.weeks.findIndex(w => w.is_current))
const today            = new Date()
const selectedTrainers = ref([])

// navigation
const prevWeek = () => currentWeekIndex.value > 0 && currentWeekIndex.value--
const nextWeek = () => currentWeekIndex.value < props.weeks.length - 1 && currentWeekIndex.value++

// pick the current week
const selectedWeek = computed(() => props.weeks[currentWeekIndex.value])

// month–year label
const monthLabel = computed(() => {
    const s = parseISO(selectedWeek.value.start)
    const e = parseISO(selectedWeek.value.end)
    const sm = format(s,'MMM').toUpperCase()
    const em = format(e,'MMM').toUpperCase()
    const yy = format(s,'yy')
    return sm === em ? `${sm} ${yy}` : `${sm}–${em} ${yy}`
})

// trainers in current week
const trainers = computed(() =>
    Array.from(new Set(selectedWeek.value.bookings.map(b => b.trainer)))
)

// header days Mon→Sat
const headerDays = computed(() => {
    const start = parseISO(selectedWeek.value.start)
    return Array.from({ length: 6 }).map((_, i) => {
        const d = addDays(start, i)
        return { short: format(d,'EEE'), day: format(d,'d'), isToday: isSameDay(d,today) }
    })
})

// times gutter 7AM→10PM
const hours = computed(() =>
    Array.from({ length: 16 }).map((_, i) =>
        setMinutes(setHours(startOfDay(new Date()), 7 + i), 0)
    )
)

// build & merge slots, union-find clusters
const allEvents = computed(() => {
    const weekStart = parseISO(selectedWeek.value.start)
    const raw = selectedWeek.value.bookings.flatMap((b, bi) =>
        b.booking_slots.map(s => {
            const start = parseISO(s.start_time)
            const end   = parseISO(s.end_time)
            const mins  = differenceInMinutes(start, startOfDay(start))
            const dayIx = differenceInCalendarDays(start, weekStart)
            if (mins<420||mins>=1320||dayIx<0||dayIx>5) return null

            const col      = dayIx+1
            const rowStart = Math.floor((mins-420)/5)+2
            const span     = Math.max(1, Math.ceil(differenceInMinutes(end,start)/5))
            const pal      = [
                {bg:'bg-blue-50 hover:bg-blue-100',   text:'text-blue-700',  hover:'text-blue-700'},
                {bg:'bg-pink-50 hover:bg-pink-100',   text:'text-pink-700',  hover:'text-pink-700'},
                {bg:'bg-gray-100 hover:bg-gray-200',  text:'text-gray-700',  hover:'text-gray-700'},
                {bg:'bg-amber-100 hover:bg-amber-200',text:'text-amber-700', hover:'text-amber-700'},
                {bg:'bg-purple-100 hover:bg-purple-200',text:'text-purple-700',hover:'text-purple-700'},
                {bg:'bg-teal-100 hover:bg-teal-200',  text:'text-teal-700',  hover:'text-teal-700'}
            ][bi%6]

            return {
                id:         s.id,
                trainer:    b.trainer,
                start_time: s.start_time,
                short_time: s.short_time,
                col, rowStart, span,
                bgClass:    pal.bg,
                textClass:  pal.text,
                hoverText:  pal.hover,
                member:     b.member
            }
        })
    ).filter(Boolean)

    // merge same-trainer+same-slot
    const map = {}
    raw.forEach(e => {
        const key = `${e.trainer}::${e.col}-${e.rowStart}-${e.span}`
        if (!map[key]) map[key] = { ...e, members: [e.member] }
        else map[key].members.push(e.member)
    })
    const merged = Object.values(map)

    // group by day & union-find overlaps
    const byDay = merged.reduce((acc,e)=>{
        (acc[e.col] ||= []).push(e)
        return acc
    }, {})
    const positioned = []
    Object.values(byDay).forEach(slots => {
        const n = slots.length
        const parent = slots.map((_,i)=>i)
        const find = i => parent[i]===i?i:(parent[i]=find(parent[i]))
        const union = (a,b)=>{ const ra=find(a), rb=find(b); if(ra!==rb) parent[rb]=ra }
        for(let i=0;i<n;i++) for(let j=i+1;j<n;j++){
            const a=slots[i], b=slots[j]
            if(a.rowStart < b.rowStart+b.span && b.rowStart < a.rowStart+a.span)
                union(i,j)
        }
        const clusters = {}
        for(let i=0;i<n;i++){
            const r = find(i)
            ;(clusters[r] ||= []).push(slots[i])
        }
        Object.values(clusters).forEach(group=>{
            group.sort((a,b)=>a.rowStart-b.rowStart||a.id-b.id)
            group.forEach((slot,idx)=>{
                slot.overlapCount = group.length
                slot.overlapIndex = idx
                positioned.push(slot)
            })
        })
    })
    return positioned
})

// apply trainer filter
const filteredEvents = computed(() =>
    selectedTrainers.value.length
        ? allEvents.value.filter(e => selectedTrainers.value.includes(e.trainer))
        : allEvents.value
)
</script>
