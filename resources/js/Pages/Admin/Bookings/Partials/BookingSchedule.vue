<template>
    <div class="">
        <div class="mt-12 text-sm flex items-center gap-3">
            <p class="font-medium">Plan the schedule for the upcoming weeks</p>
            <SecondaryButton @click="addRepeatableDay">Add day</SecondaryButton>
        </div>

        <div v-if="scheduleInfo" class="my-8 space-y-4">
            <repeatable-scheduler
                v-for="(item, index) in scheduleInfo"
                :key="index"
                v-model="scheduleInfo[index]"
                @remove="removeRepeatableDay(item)"
            ></repeatable-scheduler>
        </div>
    </div>
</template>

<script setup>
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import RepeatableScheduler from '@/Pages/Admin/Bookings/Partials/RepeatableScheduler.vue'

import { inject, ref } from 'vue'

const form = inject('form')

const scheduleInfo = ref([])

const addRepeatableDay = () => {
    scheduleInfo.value.push({
        day: 'Monday',
        time: '07:00 am'
    })
}

const removeRepeatableDay = (item) => {
    const index = scheduleInfo.value.indexOf(item)
    if (index !== -1)
        scheduleInfo.value.splice(index, 1)
}
</script>
