<template>
    <AppLayout title="New Training">
        <Container :centered="true">
            <page-title>
                <div class="pb-8 border-b border-zinc-200 w-full">Change session date and time</div>
            </page-title>

            <FormSection title="Date" description="Update the session date.">
                <DateInput v-model="form.date"></DateInput>
                <InputError :message="form.errors.date" />
            </FormSection>

            <FormSection title="Time" description="Update the session time.">
                <TimeInput v-model="form.time"></TimeInput>
                <InputError :message="form.errors.time" />
            </FormSection>

            <div class="text-right space-x-4">
                <Link :href="route('admin.bookings-slots.show', id)">
                    <TransparentButton>Cancel</TransparentButton>
                </Link>
                <primary-button @click="updateBookingSlot" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update</primary-button>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { format, addHours, parse } from 'date-fns'
import DateInput from '@/Components/Form/DateInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import TimeInput from '@/Components/Form/TimeInput.vue'
import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    bookingSlot: { type: Object, required: true },
})

const { id, date, start_time } = props.bookingSlot

const form = useForm({
    date,
    time: start_time,
    start_time: null,
    end_time: null,
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

const updateBookingSlot = () => {
    form
        .transform((data) => {
            const startTime = combineDateAndTime(data.date, data.time)
            const endTime = format(addHours(parse(startTime, 'yyyy-MM-dd HH:mm:ss', new Date()), 1), 'yyyy-MM-dd HH:mm:ss')

            return {
                ...data,
                start_time: startTime,
                end_time: endTime,
            }
        })
        .put(route('admin.bookings-slots.update', id), {
            preserveScroll: true,
            onError: (e) => {
                console.log(e)
            },
        })
}
</script>
