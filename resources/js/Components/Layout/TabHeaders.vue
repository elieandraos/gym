<template>
    <div class="relative">
        <ul ref="tabsContainer" class="flex items-center gap-4 capitalize cursor-pointer">
            <li
                v-for="(title, index) in tabTitles"
                :key="title"
                :ref="el => tabRefs[index] = el"
                class="capitalize px-3 py-1.5 transition-colors duration-300"
                :class="title === selectedTitle ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900'"
                @click="selectTab(title, index)">
                {{ title }}
            </li>
        </ul>
        <div
            class="absolute bottom-0 h-0.5 bg-zinc-900 transition-all duration-300 ease-out"
            :style="{
                left: `${underlineLeft}px`,
                width: `${underlineWidth}px`
            }"
        />
    </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'

const props = defineProps({
    tabs: { type: Array, required: true }, // Array of tab title strings
    modelValue: { type: String, default: '' }, // Selected tab (v-model support)
})

const emit = defineEmits(['update:modelValue'])

const tabTitles = ref(props.tabs)
const selectedTitle = ref(props.modelValue || props.tabs[0])
const tabsContainer = ref(null)
const tabRefs = ref([])
const underlineLeft = ref(0)
const underlineWidth = ref(0)

const updateUnderlinePosition = (index) => {
    const tabElement = tabRefs.value[index]
    const containerElement = tabsContainer.value

    if (tabElement && containerElement) {
        const containerRect = containerElement.getBoundingClientRect()
        const tabRect = tabElement.getBoundingClientRect()

        underlineLeft.value = tabRect.left - containerRect.left
        underlineWidth.value = tabRect.width
    }
}

watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        selectedTitle.value = newValue
        const index = tabTitles.value.indexOf(newValue)
        if (index !== -1) {
            nextTick(() => updateUnderlinePosition(index))
        }
    }
})

onMounted(() => {
    const initialIndex = tabTitles.value.indexOf(selectedTitle.value)
    if (initialIndex !== -1) {
        nextTick(() => updateUnderlinePosition(initialIndex))
    }
})

const selectTab = (title, index) => {
    selectedTitle.value = title
    updateUnderlinePosition(index)
    emit('update:modelValue', title)
}
</script>
