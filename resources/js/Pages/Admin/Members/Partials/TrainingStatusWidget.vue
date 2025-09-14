<template>
    <div class="bg-white rounded-lg">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0">
                <CalendarDaysIcon class="size-12 text-zinc-200" />
            </div>
            <div class="flex-1 text-[#71717b]">
                <div v-if="isTraining" class="space-y-1">
                    <p>
                        Training until <span class="font-[600] text-zinc-950">{{ activeBooking.formatted_end_date }}</span>
                    </p>
                    <p>
                        with
                        <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-sky-500 hover:text-sky-700 font-[500]">
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
