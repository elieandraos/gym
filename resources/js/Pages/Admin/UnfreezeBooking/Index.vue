<template>
    <AppLayout title="Unfreeze Training">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="space-y-8">
                <div class="">You have {{ frozenSlots.length }} frozen {{ frozenSlots.length === 1 ? 'session' : 'sessions' }} to reschedule.</div>

                <div class="space-y-4">
                    <div v-for="(slot, index) in slots" :key="slot.id" class="flex items-center gap-4">
                        <div class="w-24 text-[#71717b]">Session {{ index + 1 }}</div>
                        <DateInput v-model="slot.date" />
                        <TimeInput v-model="slot.time" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <primary-button @click="unfreezeBooking" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Reschedule
                    </primary-button>

                    <Link :href="route('admin.members.show', member.id)">
                        <TransparentButton>Cancel</TransparentButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { format, addHours, parse } from 'date-fns'
import { ref } from 'vue'

import DateInput from '@/Components/Form/DateInput.vue'
import TimeInput from '@/Components/Form/TimeInput.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    booking: { type: Object, required: true },
    frozenSlots: { type: Array, required: true },
})

const { route } = window
const { id, member } = props.booking

const slots = ref(props.frozenSlots.map(slot => ({
    id: slot.id,
    date: slot.date,
    time: slot.start_time,
})))

const form = useForm({
    slots: [],
})

const combineDateAndTime = (dateString, time) => {
    const dateObj = new Date(dateString.toLocaleString('en-US', { timeZone: 'Asia/Beirut' }))
    const [timePart, modifier] = time.split(' ')
    let [hours, minutes] = timePart.split(':').map(Number)

    if (modifier === 'PM' && hours !== 12) {
        hours += 12
    }

    if (modifier === 'AM' && hours === 12) {
        hours = 0
    }

    dateObj.setHours(hours, minutes, 0, 0)
    return format(dateObj, 'yyyy-MM-dd HH:mm:ss')
}

const unfreezeBooking = () => {
    const transformedSlots = slots.value.map(slot => {
        const startTime = combineDateAndTime(slot.date, slot.time)
        const endTime = format(addHours(parse(startTime, 'yyyy-MM-dd HH:mm:ss', new Date()), 1), 'yyyy-MM-dd HH:mm:ss')

        return {
            id: slot.id,
            start_time: startTime,
            end_time: endTime,
        }
    })

    form.slots = transformedSlots
    form.patch(route('admin.bookings.unfreeze.update', id), {
        preserveScroll: true,
    })
}
</script>
