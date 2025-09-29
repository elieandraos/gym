import { computed } from 'vue'
import { parseISO, format, setHours, setMinutes, startOfDay, differenceInMinutes } from 'date-fns'
import { useColorScheme } from './useColorScheme.js'

export function useDailyCalendarEvents(events, filters, startHour = 6, endHour = 22) {
    const { getColorScheme } = useColorScheme()

    // Generate date label
    const dateLabel = computed(() => {
        if (!filters?.date) return ''
        return format(parseISO(filters.date), 'EEEE, MMMM d, yyyy')
    })

    // Generate hours for the day (configurable range) - matching weekly calendar exactly
    const hours = computed(() => {
        const hourCount = endHour - startHour + 1
        return Array.from({ length: hourCount }).map((_, i) =>
            setMinutes(setHours(startOfDay(new Date()), startHour + i), 0)
        )
    })

    // Process and merge events with color scheme
    const rawMerged = computed(() => {
        if (!events || !Array.isArray(events)) return []

        const slots = events.map(event => {
            const start = parseISO(event.start_time)
            const end = parseISO(event.end_time)
            const mins = start.getHours() * 60 + start.getMinutes()

            // Only show events within configurable time range - matching weekly calendar
            const startMinutes = startHour * 60
            const endMinutes = endHour * 60

            if (mins < startMinutes || mins >= endMinutes) return null

            const halfHourFromStart = Math.floor((mins - startMinutes) / 30)
            const minuteInHalfHour = (mins - startMinutes) % 30
            const rowStart = halfHourFromStart + 2  // +2 for header offset (matching weekly calendar)
            const durationInMinutes = differenceInMinutes(end, start)
            const span = Math.max(1, Math.ceil(durationInMinutes / 30))

            // Calculate positioning percentages for EventCard (matching weekly calendar)
            const topPercent = (minuteInHalfHour / 30) * 100
            const heightPercent = Math.min((durationInMinutes / 30) * 100, 100)

            const colorScheme = getColorScheme(event.meta_data?.trainer_color)

            const pal = {
                bg: `${event.meta_data?.trainer_color || 'bg-gray-100'} ${colorScheme.hoverBg}`,
                text: colorScheme.text,
                hover: colorScheme.hover
            }

            return {
                id: event.id,
                url: event.url,
                trainer: event.meta_data?.trainer || 'Unknown',
                start_time: event.start_time,
                short_time: event.meta_data?.short_time || format(start, 'h:mm a'),
                col: 1, // Single column for daily view
                rowStart,
                span,
                topPercent,
                heightPercent,
                bgClass: pal.bg,
                textClass: pal.text,
                hoverText: pal.hover,
                member: event.meta_data?.member || event.title
            }
        }).filter(Boolean)

        // Merge identical slots (same trainer, same time)
        const map = {}
        slots.forEach(e => {
            const key = `${e.trainer}::${e.col}-${e.rowStart}-${e.span}`
            if (!map[key]) map[key] = { ...e, members: [e.member] }
            else map[key].members.push(e.member)
        })

        return Object.values(map)
    })

    return {
        dateLabel,
        hours,
        rawMerged
    }
}