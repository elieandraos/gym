import { computed } from 'vue'
import { parseISO, format, addDays, isSameDay, startOfDay, setHours, setMinutes, differenceInCalendarDays, differenceInMinutes } from 'date-fns'
import { useColorScheme } from './useColorScheme.js'

export function useCalendarEvents(events, filters) {
    const { getColorScheme } = useColorScheme()
    const today = new Date()

    // Month-day range label for weekly view
    const monthLabel = computed(() => {
        if (!filters?.start || !filters?.end) return ''
        const s = parseISO(filters.start)
        const e = parseISO(filters.end)
        const startFmt = format(s, 'MMM d')
        const endFmt = format(e, 'MMM d')
        return `${startFmt} - ${endFmt}`
    })

    // Header days for weekly view
    const headerDays = computed(() => {
        if (!filters?.start) return []
        const start = parseISO(filters.start)
        return Array.from({ length: 6 }).map((_, i) => {
            const d = addDays(start, i)
            return { short: format(d,'EEE'), day: format(d,'d'), isToday: isSameDay(d, today) }
        })
    })

    // Times gutter for weekly view
    const hours = computed(() =>
        Array.from({ length: 16 }).map((_, i) =>
            setMinutes(setHours(startOfDay(new Date()), 7 + i), 0)
        )
    )

    // Raw event processing and merging for weekly view
    const rawMerged = computed(() => {
        if (!filters?.start || !events) return []
        const weekStart = parseISO(filters.start)
        const slots = events.map(event => {
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

        // merge identical slots
        const map = {}
        slots.forEach(e => {
            const key = `${e.trainer}::${e.col}-${e.rowStart}-${e.span}`
            if (!map[key]) map[key] = { ...e, members: [e.member] }
            else map[key].members.push(e.member)
        })
        return Object.values(map)
    })

    // Time formatting utility
    const formatTime = (timeString) => {
        return format(parseISO(timeString), 'h:mm a')
    }

    // Date formatting for daily view
    const formattedDate = computed(() => {
        if (!filters?.date) return ''
        return format(parseISO(filters.date), 'EEEE, MMMM d, yyyy')
    })

    return {
        monthLabel,
        headerDays,
        hours,
        rawMerged,
        formatTime,
        formattedDate
    }
}