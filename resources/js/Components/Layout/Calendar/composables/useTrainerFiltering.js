import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { parseISO, format } from 'date-fns'

export function useTrainerFiltering(initialFilters, getNavParams, routeName) {
    const selectedTrainers = ref(initialFilters?.trainers || [])

    const applyTrainerFilter = (filters) => {
        if (filters?.start && filters?.end) {
            // Weekly calendar
            const currentStart = parseISO(filters.start)
            const currentEnd = parseISO(filters.end)

            router.get(route(routeName), getNavParams(currentStart, currentEnd), {
                preserveState: true,
                preserveScroll: true
            })
        } else if (filters?.date) {
            // Daily calendar
            const currentDate = parseISO(filters.date)
            const params = { date: format(currentDate, 'yyyy-MM-dd') }

            if (selectedTrainers.value.length > 0) {
                params.trainers = selectedTrainers.value.join(',')
            }

            router.get(route(routeName), params, {
                preserveState: true,
                preserveScroll: true
            })
        }
    }

    return {
        selectedTrainers,
        applyTrainerFilter
    }
}