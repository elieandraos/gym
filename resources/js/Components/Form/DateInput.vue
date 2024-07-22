<template>
    <div class="flex space-x-2 custom-date-select">
        <SelectInput size="auto" v-model="selectedDay" :options="days" placeholder="Day"></SelectInput>
        <SelectInput size="auto" v-model="selectedMonth" :options="months" placeholder="Month"></SelectInput>
        <SelectInput size="auto" v-model="selectedYear" :options="years" placeholder="Year"></SelectInput>
    </div>
</template>

<script setup>
import SelectInput from '@/Components/Form/SelectInput.vue'
import { ref, watch, computed, onBeforeMount } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Date, String, Object],
        required: true
    },
    placeholder: {
        type: String,
        default: '',
        required: false
    },
    yearRange: {
        type: Array,
        default: () => [1950, new Date().getFullYear()],
        required: false
    },
})

const emit = defineEmits(['update:modelValue'])

const days = Array.from({ length: 31 }, (_, i) => i + 1)
const months = [
    { name: 'Jan', value: 1 },
    { name: 'Feb', value: 2 },
    { name: 'Mar', value: 3 },
    { name: 'Apr', value: 4 },
    { name: 'May', value: 5 },
    { name: 'Jun', value: 6 },
    { name: 'Jul', value: 7 },
    { name: 'Aug', value: 8 },
    { name: 'Sep', value: 9 },
    { name: 'Oct', value: 10 },
    { name: 'Nov', value: 11 },
    { name: 'Dec', value: 12 }
]
const years = computed(() => {
    const [start, end] = props.yearRange
    const range = []
    for (let year = start; year <= end; year++) {
        range.push(year)
    }
    return range
})

const convertToDate = (value) => {
    if (value instanceof Date) {
        return value
    } else if (typeof value === 'string') {
        return new Date(value)
    } else if (value && typeof value.date === 'string') {
        return new Date(value.date)
    }

    return new Date()
}

const selectedDay = ref(null)
const selectedMonth = ref(null)
const selectedYear = ref(null)

const initializeValues = () => {
    const date = convertToDate(props.modelValue)
    selectedDay.value = date.getDate()
    selectedMonth.value = date.getMonth() + 1
    selectedYear.value = date.getFullYear()
}

watch(() => props.modelValue, () => {
    initializeValues()
}, { immediate: true })

watch([selectedDay, selectedMonth, selectedYear], ([newDay, newMonth, newYear]) => {
    if (newDay && newMonth && newYear) {
        const newDate = new Date(newYear, newMonth - 1, newDay)
        emit('update:modelValue', newDate)
    }
}, { immediate: true })

onBeforeMount(() => {
    initializeValues()
})
</script>

<style scoped>
.custom-date-select select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: transparent;
}

.custom-date-select::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
</style>
