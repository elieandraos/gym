<template>
    <input
        ref="input"
        v-bind="$attrs"
        class="w-full min-w-36 lg:min-w-52 p-2 border border-zinc-200 placeholder-zinc-400 hover:border-zinc-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm"
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
