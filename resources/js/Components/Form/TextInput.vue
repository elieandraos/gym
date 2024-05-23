<template>
    <input
        ref="input"
        class="w-full min-w-36 lg:min-w-52 border-zinc-300 focus:border-zinc-500 focus:ring-zinc-500 rounded-lg text-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: String,
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>
