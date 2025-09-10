<template>
    <div v-if="isTraining" class="flex items-center gap-3">
        <ArrowTrendingDownIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span>
                {{ nbRemainingSessions }} remaining
            </span>
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
const nbRemainingSessions = computed(() => activeBooking?.nb_remaining_sessions)
</script>
