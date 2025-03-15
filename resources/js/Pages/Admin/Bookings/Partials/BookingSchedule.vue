<template>
    <div class="">
        <div class="text-sm flex items-center gap-3">
            <SecondaryButton @click="addRepeatableDay">Add day</SecondaryButton>
            <InputError :message="form.errors.days" />
        </div>

        <div v-if="form.days" class="my-8 space-y-4">
            <repeatable-scheduler
                v-for="(item, index) in form.days"
                :key="index"
                v-model="form.days[index]"
                @remove="removeRepeatableDay(item)"></repeatable-scheduler>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue'

import InputError from '@/Components/Form/InputError.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import RepeatableScheduler from '@/Pages/Admin/Bookings/Partials/RepeatableScheduler.vue'

const form = inject('form')

const addRepeatableDay = () => {
    form.days.push({
        day: 'Monday',
        time: '07:00 am',
    })
}

const removeRepeatableDay = (item) => {
    const index = form.days.indexOf(item)
    if (index !== -1) form.days.splice(index, 1)
}
</script>
