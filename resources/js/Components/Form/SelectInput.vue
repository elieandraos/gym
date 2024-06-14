<template>
    <select
        v-model="model"
        v-bind="$attrs"
        class="block min-w-36 lg:min-w-52 w-full rounded-md border-zinc-300 pl-3 pr-10 py-3 text-sm focus:outline-none focus:ring-zinc-500 focus:border-zinc-500">
        <option v-for="option in options" :key="option" :value="option">
            {{ option }}
        </option>
    </select>
</template>

<script setup>
const model = defineModel()

defineProps({
    options: {
        type: Array,
        default: () => [],
    },
})
</script>
