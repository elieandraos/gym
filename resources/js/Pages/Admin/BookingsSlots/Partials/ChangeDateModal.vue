<template>
    <h2 class="font-bold text-zinc-950 capitalize">Update session</h2>
    <p class=" text-zinc-500 text-sm">Change the session date and time.</p>
    <hr class="border-t my-2">

    <div class="my-8">
        <div class="space-y-6">
            <section class="flex gap-6 items-center">
                <div class="space-y-1">
                    <h2 class="font-semibold text-zinc-950 sm:text-sm">Date</h2>
                </div>
                <div>
                    <DateInput v-model="date"></DateInput>
                </div>
            </section>
            <section class="flex gap-6 items-center">
                <div class="space-y-1">
                    <h2 class="font-semibold text-zinc-950 sm:text-sm">Time</h2>
                </div>
                <div>
                    <TimeInput v-model="start_time" />
                </div>
            </section>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <TransparentButton @click="emit('close')">exit</TransparentButton>
        <primary-button @click="changeDateAndTime">update date and time</primary-button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import DateInput from '@/Components/Form/DateInput.vue'
import TimeInput from '@/Components/Form/TimeInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

const props = defineProps({
    date: { type: [ String , Date, Object ], required: false, default: () => { new Date() } },
    start_time: { type: String, required: false, default: '07:00 AM' }
})

const emit = defineEmits(['close', 'change'])

const date = ref(props.date)
const start_time = ref(props.start_time)

const changeDateAndTime = () => {
    const dateObj = new Date(date.value); // Clone the original date
    const [time, modifier] = start_time.value.split(' '); // Split time into parts
    let [hours, minutes] = time.split(':').map(Number); // Extract hours and minutes

    // Convert to 24-hour format if necessary
    if (modifier === 'PM' && hours !== 12) {
        hours += 12;
    }
    if (modifier === 'AM' && hours === 12) {
        hours = 0;
    }

    // Set hours and minutes to the cloned Date object
    dateObj.setHours(hours, minutes);
    console.log(dateObj)
    //emit('change')
}
</script>
