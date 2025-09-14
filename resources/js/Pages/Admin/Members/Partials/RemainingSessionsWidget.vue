<template>
    <div class="bg-white rounded-lg text-[#71717b]">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0">
                <ArrowTrendingDownIcon class="size-12 text-zinc-200" />
            </div>
            <div class="flex-1">
                <div v-if="isTraining">
                    <div class="flex justify-between items-center mb-2">
                        <span class="">Sessions completed</span>
                        <span class="font-[600] text-zinc-950">{{ completedSessions }} / {{ totalSessions }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="bg-gradient-to-r from-zinc-800 to-zinc-950 h-2 rounded-full transition-all duration-500 ease-out"
                                :style="{ width: `${progressPercentage}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p class="text-gray-500">No active training</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ArrowTrendingDownIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking } = props.member

const isTraining = computed(() => !!activeBooking)
const totalSessions = computed(() => activeBooking?.nb_sessions || 0)
const remainingSessions = computed(() => {
    if (!activeBooking) return 0
    const remaining = String(activeBooking.nb_remaining_sessions).split(' ')[0]
    return parseInt(remaining) || 0
})
const completedSessions = computed(() => totalSessions.value - remainingSessions.value)
const progressPercentage = computed(() => {
    if (!totalSessions.value) return 0
    return Math.round((completedSessions.value / totalSessions.value) * 100)
})
</script>
