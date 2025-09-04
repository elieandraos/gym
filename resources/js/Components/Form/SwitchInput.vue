<template>
    <button
        type="button"
        role="switch"
        :aria-checked="model"
        @click="toggle"
        class="inline-flex items-center h-6 w-10 p-1 bg-zinc-100 border border-zinc-200 rounded-full cursor-pointer focus:outline-none  transition-colors duration-200"
    >
    <span
        :class="model ? 'translate-x-4 bg-zinc-800' : 'translate-x-0 bg-white'"
        class="inline-block w-4 h-4 rounded-full transform transition-transform duration-200"
    ></span>
    </button>
</template>

<script setup>
const model = defineModel()
const emit = defineEmits(['change'])

const toggle = () => {
    model.value = !model.value
    emit('change', model.value)
}
</script>
