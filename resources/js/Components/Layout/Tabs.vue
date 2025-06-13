<template>
    <ul class="flex justify-between items-center mb-4 border-b border-gray-200 lg:justify-start lg:gap-8 bg-white capitalize cursor-pointer">
        <li
            class="uppercase text-sm rounded-lg px-3 py-1.5 font-medium leading-relaxed tracking-widest"
            :class="title === selectedTitle ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900'"
            v-for="title in tabTitles"
            :key="title"
            @click="selectTab(title)">
            {{  title  }}
        </li>
    </ul>
    <div class="w-full mb-4">
        <slot />
    </div>
</template>

<script setup>
import {
    onMounted, ref, useSlots, provide,
} from 'vue'

const props = defineProps({
    selected: { type: String, require: false, default: '' },
})

const emit = defineEmits([
    'onTabSelected',
])

const slots = useSlots()
const selectedTitle = ref(props.selected)
const tabTitles = ref(slots.default().map((tab) => tab.props?.title))

provide('selectedTitle', selectedTitle)

const selectTab = (title) => {
    emit('onTabSelected', title)
    selectedTitle.value = title
}

onMounted(() => {
    if (!selectedTitle.value) selectedTitle.value = tabTitles.value[0]
})
</script>
