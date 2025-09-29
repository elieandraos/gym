import { computed } from 'vue'

export function useEventPositioning(rawMerged) {
    // Complex clustering and positioning algorithm for weekly grid
    const filteredEvents = computed(() => {
        const byDay = rawMerged.value.reduce((acc, e) => {
            ;(acc[e.col] ||= []).push(e)
            return acc
        }, {})

        const positioned = []

        Object.values(byDay).forEach(slots => {
            const n = slots.length
            const parent = slots.map((_, i) => i)
            const find = i => parent[i] === i ? i : (parent[i] = find(parent[i]))
            const union = (a, b) => {
                const ra = find(a), rb = find(b)
                if (ra !== rb) parent[rb] = ra
            }

            // Find overlapping slots
            for (let i = 0; i < n; i++) {
                for (let j = i + 1; j < n; j++) {
                    const a = slots[i], b = slots[j]
                    if (a.rowStart < b.rowStart + b.span && b.rowStart < a.rowStart + a.span) {
                        union(i, j)
                    }
                }
            }

            // Group slots into clusters
            const clusters = {}
            for (let i = 0; i < n; i++) {
                const r = find(i)
                ;(clusters[r] ||= []).push(slots[i])
            }

            // Position slots within each cluster
            Object.values(clusters).forEach(group => {
                group.sort((a, b) => a.rowStart - b.rowStart || a.id - b.id)
                group.forEach((slot, idx) => {
                    slot.overlapCount = group.length
                    slot.overlapIndex = idx
                    positioned.push(slot)
                })
            })
        })

        return positioned
    })

    return {
        filteredEvents
    }
}