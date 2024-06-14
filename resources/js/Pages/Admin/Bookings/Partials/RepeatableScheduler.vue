<template>
    <div class="flex items-center justify-start gap-2 text-sm">
        <span class="text-zinc-400">Every</span>
        <SelectInput size="default" v-model="selectedDay" :options="days" @change="emit('update:modelValue', dayAndTime)"></SelectInput>
        <span class="text-zinc-400">@</span>
        <SelectInput size="default" v-model="selectedHour" :options="hours" @change="emit('update:modelValue', dayAndTime)"></SelectInput>
        <span class="text-zinc-400">:</span>
        <SelectInput size="default" v-model="selectedMinute" :options="minutes" @change="emit('update:modelValue', dayAndTime)"></SelectInput>
        <SelectInput size="default" v-model="selectedTime" :options="time" @change="emit('update:modelValue', dayAndTime)"></SelectInput>

        <x-circle-icon class="w-6 h-6 text-zinc-400 hover:text-red-500 cursor-pointer" @click="emit('remove', dayAndTime)"></x-circle-icon>
    </div>
</template>

<script setup>
import SelectInput from '@/Components/Form/SelectInput.vue'
import { ref, computed, watch } from 'vue'
import { XCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: Object,
})

const emit = defineEmits(['update:modelValue', 'remove'])

const days = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ]
const hours = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ]
const minutes = ['00', '15', '30', '45']
const time = [ 'am', 'pm' ]

const selectedDay = ref('Monday')
const selectedHour = ref('07')
const selectedMinute = ref('00')
const selectedTime = ref('am')

watch(() => props.modelValue, value => {
    const [timeWithoutSpace, amPm] = value.time.split(' ')
    const [hour, minute] = timeWithoutSpace.split(':')

    selectedDay.value = value.day
    selectedHour.value = hour
    selectedMinute.value = minute
    selectedTime.value = amPm

}, { immediate: true, deep: true })

const dayAndTime = computed( () => {
    return {
        day: selectedDay.value,
        time: `${selectedHour.value}:${selectedMinute.value} ${selectedTime.value}`
    }
})
</script>
