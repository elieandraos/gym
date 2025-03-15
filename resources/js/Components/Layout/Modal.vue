<template>
    <div class="w-full h-full z-40 bg-black bg-opacity-30 fixed top-0 left-0" v-show="isOpen">
        <div class="w-full h-screen absolute flex flex-col items-center justify-center">
            <div class="px-8 pb-2 bg-white z-50 rounded-xl min-h-[20%] max-h-[60%] min-w-[35%] overflow-y-scroll" ref="modal">
                <div class="py-4">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onClickOutside } from '@vueuse/core'
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
})

const emit = defineEmits([
    'update:modelValue',
])

const modal = ref(null)
const isOpen = ref(false)

watch(() => props.modelValue, (value) => {
    isOpen.value = value
}, { immediate: true })

const close = () => {
    isOpen.value = false
    emit('update:modelValue', isOpen.value)
}

onClickOutside(modal, () => close())
</script>
