<template>
    <AppLayout title="Create Booking">
        <Container>
            <page-back-button>Back</page-back-button>

            <page-section-title class="mb-3">Training details</page-section-title>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-x-12 lg:gap-y-6 my-4">
                <div>
                    <InputLabel value="Member" />
                    <InputAutocomplete :options="membersList" v-model="form.member_id" placeholder="Choose member">
                        <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                            <li v-for="option in options" :key="option.value"
                                class="flex items-center gap-4 cursor-pointer p-3 text-sm hover:bg-stone-200"
                                @mousedown.prevent="selectOption(option)"
                            >
                                <img :src="option.profile_photo_url"  alt="" class="w-8 h-8 rounded-full"/>
                                <span v-html="highlightSearch(option.label, searchString)" class="text-zinc-500"></span>
                            </li>
                        </template>
                    </InputAutocomplete>
                    <InputError :message="form.errors.member_id" />
                </div>
                <div>
                    <InputLabel value="Trainer" />
                    <InputAutocomplete :options="trainersList" v-model="form.trainer_id" placeholder="Choose trainer">
                        <template #list-item-preview="{ options, selectOption, searchString, highlightSearch }">
                            <li v-for="option in options" :key="option.value"
                                class="flex items-center gap-4 cursor-pointer p-3 text-sm hover:bg-stone-200"
                                @mousedown.prevent="selectOption(option)"
                            >
                                <img :src="option.profile_photo_url"  alt="" class="w-8 h-8 rounded-full"/>
                                <span v-html="highlightSearch(option.label, searchString)" class="text-zinc-500"></span>
                            </li>
                        </template>
                    </InputAutocomplete>
                    <InputError :message="form.errors.trainer_id" />
                </div>
                <div>
                    <InputLabel value="Start Date" />
                    <DatepickerInput v-model="form.start_date"></DatepickerInput>
                    <InputError :message="form.errors.start_date" />
                </div>
                <div>
                    <InputLabel value="Number of sessions" />
                    <TextInput v-model="form.nb_sessions"></TextInput>
                    <InputError :message="form.errors.nb_sessions" />
                </div>
            </div>

            <booking-schedule></booking-schedule>

            <div>
                <primary-button @click="saveBooking" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Save training</primary-button>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import DatepickerInput from '@/Components/Form/DatepickerInput.vue'
import BookingSchedule from '@/Pages/Admin/Bookings/Partials/BookingSchedule.vue'
import InputAutocomplete from '@/Components/Form/InputAutocomplete.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PageSectionTitle from '@/Components/Layout/PageSectionTitle.vue'
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

const saveBooking = () => form.post(route('admin.bookings.store'), {
    preserveScroll: true,
    onError: (e) => {
        console.log(e)
    }
})
</script>
