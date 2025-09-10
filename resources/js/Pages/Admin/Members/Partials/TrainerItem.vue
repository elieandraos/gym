<template>
    <div v-if="isTraining" class="flex items-center gap-3">
        <UserIcon class="size-6 text-gray-500 flex-shrink-0" />
        <div class="flex-grow">
            <span>
                Training with
                <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-blue-600 hover:text-blue-800 font-medium">
                    {{ trainer.name }}
                </Link>
            </span>
        </div>
    </div>
</template>

<script setup>
import { UserIcon } from '@heroicons/vue/24/outline'
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
