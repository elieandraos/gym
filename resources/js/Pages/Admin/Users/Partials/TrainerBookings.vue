<template>
    <div>
        <h3 class="font-medium pb-2 mb-2 border-b border-b-zinc-200 -mt-1">
            <span class="w-7 h-7 rounded-full bg-stone-200 inline-flex justify-center items-center text-base">{{ bookings.length }}</span>
            members training with {{ first_name }}
        </h3>
        <ul class="h-[550px] overflow-y-scroll">
            <li v-for="{ member, upcoming_session, nb_remaining_sessions } in bookings" class="py-4 flex border-b border-zinc-200 last:border-0">
                <div class="flex items-center justify-between gap-2 w-full">
                    <img :src="member.profile_photo_url"  :alt="member.name" class="rounded-full w-10"/>
                    <div>
                        <h4 class="font-medium text-sm">{{ member.name }}</h4>
                        <div class="text-xs/6 text-zinc-400 flex gap-1">
                            <CalendarIcon class="w-4 text-zinc-400"></CalendarIcon>
                            <span class="font-medium">{{ upcoming_session }}</span>
                        </div>
                    </div>
                    <div class="grow flex justify-end">
                        <Badge type="success">{{ nb_remaining_sessions }} sessions remaining</Badge>
                    </div>
                </div>

            </li>
        </ul>
    </div>
</template>

<script setup>
import Badge from '@/Components/Layout/Badge.vue'
import { CalendarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    user: { type: Object, required: true },
    bookings: { type: Array, required: true }
})

const { first_name } = props.user
</script>
