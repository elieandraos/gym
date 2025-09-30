import { onMounted, nextTick } from 'vue'

export function useCalendarAutoScroll(containerRef, startHour = 6, endHour = 22, enabled = true) {
    onMounted(async () => {
        if (!enabled) return

        await nextTick()

        const now = new Date()
        const currentHour = now.getHours()
        const currentMinutes = now.getMinutes()

        // Only scroll if current time is within calendar hours
        if (currentHour >= startHour && currentHour < endHour) {
            // Small delay to ensure layout is complete
            setTimeout(() => {
                // Calculate position: each hour has 2 rows (hour + 30min marker) at 3.5rem each
                const hoursSinceStart = currentHour - startHour
                const minuteProgress = currentMinutes / 60
                const rowsFromStart = hoursSinceStart * 2 + minuteProgress * 2

                // Each row is 3.5rem, plus 1rem offset at the top
                const remInPixels = parseFloat(getComputedStyle(document.documentElement).fontSize)
                const scrollPosition = (1 + rowsFromStart * 3.5) * remInPixels

                // Scroll to position minus some offset for better visibility
                if (containerRef.value) {
                    containerRef.value.scrollTop = scrollPosition - 100
                }
            }, 100)
        }
    })
}