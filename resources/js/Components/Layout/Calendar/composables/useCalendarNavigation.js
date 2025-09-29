import { router } from '@inertiajs/vue3'
import { addDays, parseISO, format } from 'date-fns'

export function useCalendarNavigation(filters, selectedTrainers, routeName) {
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
        if (!filters?.start) return
        const currentStart = parseISO(filters.start)
        const newStart = addDays(currentStart, -7)
        const newEnd = addDays(newStart, 5)

        router.get(route(routeName), getNavParams(newStart, newEnd))
    }

    const nextWeek = () => {
        if (!filters?.start) return
        const currentStart = parseISO(filters.start)
        const newStart = addDays(currentStart, 7)
        const newEnd = addDays(newStart, 5)

        router.get(route(routeName), getNavParams(newStart, newEnd))
    }

    const prevDay = () => {
        if (!filters?.date) return
        const currentDate = parseISO(filters.date)
        const newDate = addDays(currentDate, -1)

        const params = { date: format(newDate, 'yyyy-MM-dd') }
        if (selectedTrainers.value.length > 0) {
            params.trainers = selectedTrainers.value.join(',')
        }

        router.get(route(routeName), params)
    }

    const nextDay = () => {
        if (!filters?.date) return
        const currentDate = parseISO(filters.date)
        const newDate = addDays(currentDate, 1)

        const params = { date: format(newDate, 'yyyy-MM-dd') }
        if (selectedTrainers.value.length > 0) {
            params.trainers = selectedTrainers.value.join(',')
        }

        router.get(route(routeName), params)
    }

    return {
        getNavParams,
        prevWeek,
        nextWeek,
        prevDay,
        nextDay
    }
}