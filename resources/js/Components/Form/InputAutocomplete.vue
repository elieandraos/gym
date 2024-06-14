<template>
    <div class="relative w-full">
        <TextInput v-bind="$attrs" v-model="model" @keyup="filterOptions" v-if="!selectedOption" ref="textInput"></TextInput>

        <div
            v-else
            class="flex items-center justify-between w-full min-w-36 lg:min-w-52 border border-indigo-300 bg-indigo-50 text-indigo-900 font-medium rounded-lg text-sm p-3"
        >
            <span>{{ selectedOption.label }}</span>
            <x-mark-icon class="w-4 h-4 text-indigo-400 cursor-pointer" @click="clearSelection">x</x-mark-icon>
        </div>

        <div v-if="showResults" class="absolute z-10 bg-white border border-zinc-300 rounded-lg mt-1 w-full shadow-sm">
            <ul>
                <slot name="list-item-preview" :options="filteredOptions.slice(0, maxResults)" :selectOption="selectOption" :highlightSearch="highlightSearch" :searchString="model">
                    <li v-for="option in filteredOptions.slice(0, maxResults)" :key="option.value"
                        class="cursor-pointer p-3 text-sm hover:bg-stone-200"
                        @mousedown.prevent="selectOption(option)"
                    >
                        <span v-html="highlightSearch(option.label, model)" class="text-zinc-700"></span>
                    </li>
                </slot>
            </ul>
        </div>

    </div>
</template>

<script setup>
import TextInput from '@/Components/Form/TextInput.vue'
import { XMarkIcon } from '@heroicons/vue/24/solid/index.js'
import { ref, computed, watch, nextTick } from 'vue'

const props = defineProps({
    options: { type: Array, required: true }
})

const model = defineModel()

const filteredOptions = ref([])
const selectedOption = ref(null)
const textInput = ref(null)
const maxResults = 5

const filterOptions = () => {
    const inputValue = model.value ? model.value.toString() : ''

    if (inputValue === '') {
        filteredOptions.value = []
        return
    }

    filteredOptions.value = props.options.filter( option => option.label.toLowerCase().includes(inputValue.toLowerCase()))
}

const selectOption = (option) => {
    model.value = option.value
    selectedOption.value = option
}

const clearSelection = () => {
    selectedOption.value = null
    model.value = ''
    filteredOptions.value = []
    nextTick(() => {
        textInput.value.focus()
    })
}

const showResults = computed( () => filteredOptions.value.length && !selectedOption.value)

watch(model, (newValue) => {
    selectedOption.value = props.options.find(opt => opt.value === newValue) || null
}, { immediate: true })

const highlightSearch = (text, search) => {
    if (!search) return text
    const regex = new RegExp(`(${search})`, 'gi')
    return text.replace(regex, '<span class="font-bold text-zinc-900">$1</span>')
}
</script>
