<template>
    <div class="relative inline-block" ref="root">
        <div @click.stop="toggle">
            <slot name="trigger">
                <EllipsisHorizontalIcon
                    class="w-6 h-6 cursor-pointer text-zinc-500 hover:bg-zinc-100 hover:rounded"
                />
            </slot>
        </div>

        <!-- Invisible bridge to prevent gap issues -->
        <div
            v-if="open"
            class="absolute w-full h-2 top-full"
            :class="alignmentClass"
        ></div>

        <div
            v-if="open"
            class="absolute z-50 rounded-md border border-gray-200 bg-white text-sm font-normal text-zinc-500 p-4 min-w-56 shadow-sm mt-2"
            :class="alignmentClass"
            @click.stop
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
        default: 'left', // 'left', 'right', or 'center'
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
            return 'right-0'
        case 'center':
            return 'left-1/2 -translate-x-1/2'
        case 'right':
        default:
            return 'left-0'
    }
})

onClickOutside(root, () => {
    open.value = false
})
</script>
