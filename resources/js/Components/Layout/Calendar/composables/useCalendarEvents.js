import { computed } from 'vue'
import { parseISO, format, addDays, isSameDay, startOfDay, setHours, setMinutes, differenceInCalendarDays, differenceInMinutes } from 'date-fns'
import { useColorScheme } from './useColorScheme.js'

export function useCalendarEvents(events, filters, startHour = 6, endHour = 22) {
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
        if (!filters?.start || !filters?.end) return []
        const start = parseISO(filters.start)
        const end = parseISO(filters.end)
        const dayCount = differenceInCalendarDays(end, start) + 1
        return Array.from({ length: dayCount }).map((_, i) => {
            const d = addDays(start, i)
            return { short: format(d,'EEE'), day: format(d,'d'), isToday: isSameDay(d, today) }
        })
    })

    // Times gutter for weekly view (configurable range)
    const hours = computed(() => {
        const hourCount = endHour - startHour + 1
        return Array.from({ length: hourCount }).map((_, i) =>
            setMinutes(setHours(startOfDay(new Date()), startHour + i), 0)
        )
    })

    // Raw event processing and merging for weekly view
    const rawMerged = computed(() => {
        if (!filters?.start || !filters?.end || !events) return []
        const weekStart = parseISO(filters.start)
        const weekEnd = parseISO(filters.end)
        const maxDays = differenceInCalendarDays(weekEnd, weekStart)
        const slots = events.map(event => {
            const start  = parseISO(event.start_time)
            const end    = parseISO(event.end_time)
            const mins   = differenceInMinutes(start, startOfDay(start))
            const dayIx  = differenceInCalendarDays(start, weekStart)
            const startMinutes = startHour * 60
            const endMinutes = endHour * 60

            if (mins < startMinutes || mins >= endMinutes || dayIx < 0 || dayIx > maxDays) return null

            const col       = dayIx + 1
            const quarterHourFromStart = Math.floor((mins - startMinutes) / 15)
            const minuteInQuarterHour = (mins - startMinutes) % 15
            const rowStart  = quarterHourFromStart + 2  // +2 for header offset
            const durationInMinutes = differenceInMinutes(end, start)
            const span      = Math.max(1, Math.ceil(durationInMinutes / 15))

            // Calculate precise positioning within the 15-minute block (0-100%)
            const topPercent = (minuteInQuarterHour / 15) * 100
            const heightPercent = Math.min((durationInMinutes / 15) * 100, 100)
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
                member:     event.meta_data.member,
                member_photo_url: event.meta_data.member_photo_url,
                topPercent,
                heightPercent
            }
        }).filter(Boolean)

        // merge identical slots
        const map = {}
        slots.forEach(e => {
            const key = `${e.trainer}::${e.col}-${e.rowStart}-${e.span}`
            if (!map[key]) {
                map[key] = { ...e, members: [e.member], member_photos: [e.member_photo_url] }
            } else {
                map[key].members.push(e.member)
                map[key].member_photos.push(e.member_photo_url)
            }
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