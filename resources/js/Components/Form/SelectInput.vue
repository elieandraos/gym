<template>
    <div class="relative custom-select">
        <select
            v-model="model"
            ref="selectInput"
            v-bind="$attrs"
            class="p-2 text-sm border border-zinc-200 hover:border-zinc-300 rounded-lg focus:outline-none focus:ring-0 bg-none cursor-pointer appearance-none"
            :class="[size !== 'auto' ? 'min-w-36 lg:min-w-52 w-full' : 'min-w-12', model === '' ? 'text-zinc-400' : '']"
        >
            <option disabled value="" class="text-zinc-300" v-if="placeholder">{{ placeholder }}</option>
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>
        <span class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <ChevronUpDownIcon class="w-5 h-5 text-zinc-400"></ChevronUpDownIcon>
        </span>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { ChevronUpDownIcon } from '@heroicons/vue/24/solid/index.js'

const props = defineProps({
    options: {
        type: Array,
        default: () => [],
        required: true
    },
    size: {
        type: String,
        default: '',
        required: false
    },
    placeholder: {
        type: String,
        default: '',
        required: false
    },
})

const model = ref('')

watch(() => props.options, () => {
    if (!model.value && props.placeholder) {
        model.value = ''
    }
});
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
