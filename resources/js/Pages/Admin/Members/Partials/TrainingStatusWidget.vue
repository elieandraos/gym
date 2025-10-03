<template>
    <div class="bg-white rounded-lg">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0">
                <CalendarDaysIcon class="size-12 text-zinc-200" />
            </div>
            <div class="flex-1 text-[#71717b]">
                <div v-if="props.isTraining" class="space-y-1">
                    <div v-if="activeBooking.is_frozen">
                        <div class="flex items-center gap-2">
                            <span>Training is</span>
                            <Badge type="info">frozen</Badge>
                        </div>
                        <a href="#" class="text-sky-500 hover:text-sky-700 font-[500]">
                            Unfreeze
                        </a>
                    </div>
                    <div v-else>
                        <p>
                            Training until <span class="font-[600] text-zinc-950">{{ formatted_end_date }}</span>
                        </p>
                        <p>
                            with
                            <Link :href="route('admin.trainers.show', { user: trainer.id })" class="text-sky-500 hover:text-sky-700 font-[500]">
                                {{ trainer.name }}
                            </Link>
                        </p>
                    </div>
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
import Badge from '@/Components/Layout/Badge.vue'

const { route } = window

const props = defineProps({
    isTraining: { type: Boolean, required: true },
    activeBooking: { type: Object, default: null },
})

const { formatted_end_date } = props.activeBooking || {}
const trainer = computed(() => props.activeBooking?.trainer)
</script>
