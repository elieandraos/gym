<template>
    <AppLayout title="New Training">
        <Container>
            <PageHeader :sticky="true">
                <PageHeaderTitle>New training</PageHeaderTitle>
            </PageHeader>

            <FormSection title="Member" description="Choose the member receiving the training.">
                <InputAutocomplete :options="membersList" v-model="form.member_id" placeholder="">
                    <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                        <li v-for="option in options" :key="option.value"
                            class="flex items-center gap-4 cursor-pointer p-2 text-zinc-500 hover:bg-zinc-100"
                            @mousedown.prevent="selectOption(option)">
                            <img :src="option.profile_photo_url"  alt="" class="size-6 rounded-full"/>
                            <span v-html="highlightSearch(option.label, searchString)"></span>
                        </li>
                    </template>
                </InputAutocomplete>
                <InputError :message="form.errors.member_id" />
            </FormSection>

            <FormSection title="Trainer" description="Choose the trainer giving the training.">
                <InputAutocomplete :options="trainersList" v-model="form.trainer_id" placeholder="">
                    <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                        <li v-for="option in options" :key="option.value"
                            class="flex items-center gap-4 cursor-pointer p-2 text-zinc-500 hover:bg-zinc-100"
                            @mousedown.prevent="selectOption(option)">
                            <img :src="option.profile_photo_url"  alt="" class="size-6 rounded-full"/>
                            <span v-html="highlightSearch(option.label, searchString)"></span>
                        </li>
                    </template>
                </InputAutocomplete>
                <InputError :message="form.errors.trainer_id" />
            </FormSection>

            <FormSection title="Start date" description="Enter the training starting date.">
                <DateInput v-model="form.start_date"></DateInput>
                <InputError :message="form.errors.start_date" />
            </FormSection>

            <FormSection title="# sessions" description="Enter the number of sessions for this training.">
                <TextInput v-model="form.nb_sessions"></TextInput>
                <InputError :message="form.errors.nb_sessions" />
            </FormSection>

            <FormSection title="Payment status" description="Mark whether this training has been paid for.">
                <div class="flex items-center gap-3">
                    <SwitchInput v-model="form.is_paid" />
                    <span class="text-[#71717b]">{{ form.is_paid ? 'Paid' : 'Unpaid' }}</span>
                </div>
                <InputError :message="form.errors.is_paid" />
            </FormSection>

            <FormSection title="Schedule" description="Plan the training days for the upcoming weeks.">
                <booking-schedule></booking-schedule>
            </FormSection>

            <div class="text-right">
                <primary-button @click="saveBooking" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Save training</primary-button>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, provide } from 'vue'

import DateInput from '@/Components/Form/DateInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputAutocomplete from '@/Components/Form/InputAutocomplete.vue'
import InputError from '@/Components/Form/InputError.vue'
import SwitchInput from '@/Components/Form/SwitchInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import BookingSchedule from '@/Pages/Admin/Bookings/Partials/BookingSchedule.vue'

const props = defineProps({
    members: { type: Array, required: true },
    trainers: { type: Array, required: true },
})

const form = useForm({
    member_id: null,
    trainer_id: null,
    start_date: new Date(),
    nb_sessions: 12,
    is_paid: true,
    days: [],
})

provide('form', form)

const membersList = computed(() => props.members.map(({ id, name, profile_photo_url }) => ({ label: name, value: id, profile_photo_url })))

const trainersList = computed(() => props.trainers.map(({ id, name, profile_photo_url }) => ({ label: name, value: id, profile_photo_url })))

const saveBooking = () => {
    form.post(route('admin.bookings.store'), {
        preserveScroll: true,
        onError: (e) => {
            console.log(e)
        },
    })
}
</script>
