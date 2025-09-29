import { parseISO, format, addDays } from 'date-fns'
import { router } from '@inertiajs/vue3'

export function useDailyCalendarNavigation(filters, selectedTrainers, routeName) {
    const { route } = window

    const getNavParams = (date) => {
        const params = {
            date: format(date, 'yyyy-MM-dd')
        }

        if (selectedTrainers.value.length > 0) {
            params.trainers = selectedTrainers.value.join(',')
        }

        return params
    }

    const prevDay = () => {
        if (!filters?.date) return
        const currentDate = parseISO(filters.date)
        const newDate = addDays(currentDate, -1)

        router.get(route(routeName), getNavParams(newDate))
    }

    const nextDay = () => {
        if (!filters?.date) return
        const currentDate = parseISO(filters.date)
        const newDate = addDays(currentDate, 1)

        router.get(route(routeName), getNavParams(newDate))
    }

    return {
        prevDay,
        nextDay,
        getNavParams
    }
}