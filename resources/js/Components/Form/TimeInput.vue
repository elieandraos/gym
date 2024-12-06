<template>
    <div class="flex space-x-2 items-center">
        <SelectInput size="auto" v-model="selectedHour" :options="hours" @change="emit('update:modelValue', timeString)"></SelectInput>
        <SelectInput size="auto" v-model="selectedMinute" :options="minutes" @change="emit('update:modelValue', timeString)"></SelectInput>
        <SelectInput size="auto" v-model="selectedTime" :options="time" @change="emit('update:modelValue', timeString)"></SelectInput>
    </div>
</template>

<script setup>
import SelectInput from '@/Components/Form/SelectInput.vue'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    modelValue: String,
})

const emit = defineEmits(['update:modelValue', 'remove'])

const hours = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ]
const minutes = ['00', '15', '30', '45']
const time = [ 'AM', 'PM' ]

const selectedHour = ref('07')
const selectedMinute = ref('00')
const selectedTime = ref('AM')

watch(() => props.modelValue, value => {

    const [timeWithoutSpace, amPm] = value.split(' ')
    const [hour, minute] = timeWithoutSpace.split(':')

    selectedHour.value = hour
    selectedMinute.value = minute
    selectedTime.value = amPm

}, { immediate: true, deep: true })

const timeString = computed( () => `${selectedHour.value}:${selectedMinute.value} ${selectedTime.value}` )
</script>
