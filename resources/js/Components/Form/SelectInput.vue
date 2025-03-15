<template>
    <div class="relative custom-select">
        <select
            v-model="model"
            ref="selectInput"
            v-bind="$attrs"
            class="p-2 text-sm border border-zinc-200 hover:border-zinc-300 rounded-lg focus:outline-none focus:ring-0 bg-none cursor-pointer appearance-none"
            :class="[size !== 'auto' ? 'min-w-36 lg:min-w-52 w-full' : 'min-w-[70px]', model === '' || !model ? 'text-zinc-400' : '']">
            <option disabled value="" class="text-zinc-300" v-if="placeholder">{{ placeholder }}</option>
            <option v-for="option in options" :key="optionKey(option)" :value="optionValue(option)">
                {{ optionName(option) }}
            </option>
        </select>
        <span class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <ChevronUpDownIcon class="w-5 h-5 text-zinc-400"></ChevronUpDownIcon>
        </span>
    </div>
</template>

<script setup>
import { ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: '',
        required: true,
    },
    options: {
        type: Array,
        default: () => [],
        required: true,
    },
    size: {
        type: String,
        default: '',
        required: false,
    },
    placeholder: {
        type: String,
        default: '',
        required: false,
    },
})

const emit = defineEmits(['update:modelValue'])
const model = ref(props.modelValue)

watch(model, (newValue) => {
    emit('update:modelValue', newValue)
})

watch(() => props.modelValue, (newValue) => {
    model.value = newValue
}, { immediate: true })

watch(() => props.options, () => {
    if (!model.value && props.placeholder) {
        model.value = ''
    }
}, { immediate: true })

const optionKey = (option) => (typeof option === 'object' ? option.value : option)

const optionValue = (option) => (typeof option === 'object' ? option.value : option)

const optionName = (option) => (typeof option === 'object' ? option.name : option)
</script>

<style scoped>
.custom-select select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: transparent;
}

.custom-select::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
</style>
