<template>
    <AppLayout title="New Training">
        <Container :centered="true">
            <page-title>
                <div class="pb-8 border-b border-zinc-200 w-full">New training</div>
            </page-title>

            <FormSection title="Member" description="Choose the member receiving the training.">
                <InputAutocomplete :options="membersList" v-model="form.member_id" placeholder="Choose member">
                    <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                        <li v-for="option in options" :key="option.value"
                            class="flex items-center gap-4 cursor-pointer p-2 text-sm hover:bg-indigo-200"
                            @mousedown.prevent="selectOption(option)"
                        >
                            <img :src="option.profile_photo_url"  alt="" class="w-6 h-6 rounded-full"/>
                            <span v-html="highlightSearch(option.label, searchString)" class="text-zinc-950"></span>
                        </li>
                    </template>
                </InputAutocomplete>
                <InputError :message="form.errors.member_id" />
            </FormSection>

            <FormSection title="Trainer" description="Choose the trainer giving the training.">
                <InputAutocomplete :options="trainersList" v-model="form.trainer_id" placeholder="Choose trainer">
                    <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                        <li v-for="option in options" :key="option.value"
                            class="flex items-center gap-4 cursor-pointer p-2 text-sm hover:bg-indigo-200"
                            @mousedown.prevent="selectOption(option)"
                        >
                            <img :src="option.profile_photo_url"  alt="" class="w-6 h-6 text-indigo-700 rounded-full"/>
                            <span v-html="highlightSearch(option.label, searchString)" class="text-zinc-950"></span>
                        </li>
                    </template>
                </InputAutocomplete>
                <InputError :message="form.errors.trainer_id" />
            </FormSection>

            <FormSection title="Start date" description="Enter the training starting date.">
                <DatepickerInput v-model="form.start_date"></DatepickerInput>
                <InputError :message="form.errors.start_date" />
            </FormSection>

            <FormSection title="# sessions" description="Enter the number of sessions for this training.">
                <TextInput v-model="form.nb_sessions"></TextInput>
                <InputError :message="form.errors.nb_sessions" />
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
import DatepickerInput from '@/Components/Form/DatepickerInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import BookingSchedule from '@/Pages/Admin/Bookings/Partials/BookingSchedule.vue'
import InputAutocomplete from '@/Components/Form/InputAutocomplete.vue'
import InputError from '@/Components/Form/InputError.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Container from '@/Components/Layout/Container.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

import { useForm } from '@inertiajs/vue3'
import { computed, provide } from 'vue'

const props = defineProps({
    members: { type: Array, required: true },
    trainers: { type: Array, required: true },
})

const form = useForm({
    member_id: null,
    trainer_id: null,
    start_date: new Date(),
    nb_sessions: 12,
    days: []
})

provide('form', form)

const membersList = computed( () => {
    return props.members.map( function({id, name, profile_photo_url}) {
        return { label: name, value: id, profile_photo_url: profile_photo_url }
    })
})

const trainersList = computed( () => {
    return props.trainers.map( function({id, name, profile_photo_url}) {
        return { label: name, value: id, profile_photo_url: profile_photo_url }
    })
})

const saveBooking = () => {
    form.post(route('admin.bookings.store'), {
        preserveScroll: true,
        onError: (e) => {
            console.log(e)
        }
    })
}
</script>
