<template>
    <select
        v-model="model"
        v-bind="$attrs"
        class="block rounded-md border-zinc-300 pl-3 pr-10 py-3 text-sm focus:outline-none focus:ring-zinc-500 focus:border-zinc-500"
        :class="size !== 'default' ? 'min-w-36 lg:min-w-52 w-full' : ''"
    >
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
        required: true
    },
    size: {
        type: String,
        default: '',
        required: false
    }
})
</script>
