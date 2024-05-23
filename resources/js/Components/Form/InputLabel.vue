<template>
    <label class="block font-medium text-sm text-gray-700 mb-0.5">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<script setup>
defineProps({
    value: String,
})
</script>
