<template>
    <div class="bg-white rounded-lg">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <CalendarDaysIcon class="size-8 text-[#71717b]" />
            </div>
            <div class="flex-1 text-[#71717b]">
                <div v-if="isTraining" class="space-y-1">
                    <p>
                        Training until <span class="font-[600] text-zinc-950">{{ activeBooking.formatted_end_date }}</span>
                    </p>
                    <p>
                        with
                        <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ trainer.name }}
                        </Link>
                    </p>
                </div>
                <div v-else>
                    <p>Currently not training</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const { route } = window

const props = defineProps({
    member: { type: Object, required: true },
})

const { active_booking: activeBooking } = props.member

const isTraining = computed(() => !!activeBooking)
const trainer = computed(() => activeBooking?.trainer)
</script>
