<template>
    <select
        v-model="selectedOption"
        class="block min-w-36 lg:min-w-52 w-full rounded-md border-zinc-300 pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-zinc-500 focus:border-zinc-500">
        <option v-for="option in options" :key="option" :value="option">
            {{ option }}
        </option>
    </select>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, null],
        required: true,
        default: null
    },
    options: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['update:modelValue'])

const selectedOption = ref(props.modelValue)

watch(selectedOption, (newValue) => {
    emit('update:modelValue', newValue)
})
</script>
