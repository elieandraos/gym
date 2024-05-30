<template>
    <input
        ref="input"
        className="w-full min-w-36 lg:min-w-52 border-zinc-300 focus:border-zinc-500 focus:ring-zinc-500 rounded-lg text-sm"
        v-model="model"/>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const model = defineModel()

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})

defineExpose({ focus: () => input.value.focus() })
</script>
