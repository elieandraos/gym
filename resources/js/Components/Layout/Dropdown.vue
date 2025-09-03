<template>
    <div class="relative inline-block" ref="root">
        <EllipsisHorizontalIcon
            class="w-6 h-6 cursor-pointer text-zinc-500 hover:bg-zinc-100 hover:rounded"
            @click="toggle"
        />

        <div
            v-if="open"
            class="absolute z-50 rounded-md border border-gray-200 bg-white text-sm font-normal text-zinc-500 p-4 min-w-56 shadow-sm"
            :class="alignmentClass"
        >
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { EllipsisHorizontalIcon } from '@heroicons/vue/24/outline'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    direction: {
        type: String,
        default: 'right', // 'left', 'right', or 'center'
    },
})

const open = ref(false)
const root = ref(null)

const toggle = () => {
    open.value = !open.value
}

const alignmentClass = computed(() => {
    switch (props.direction) {
        case 'left':
            return 'left-0'
        case 'center':
            return 'left-1/2 -translate-x-1/2'
        case 'right':
        default:
            return 'right-0'
    }
})

onClickOutside(root, () => {
    open.value = false
})
</script>
