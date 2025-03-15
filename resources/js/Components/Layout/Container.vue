<template>
    <div class="p-6 lg:p-10" :class="classList">
        <slot></slot>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    centered: { type: Boolean, required: false, default: false },
})

const classList = computed(() => (props.centered === true ? 'max-w-4xl mx-auto' : 'max-w-7xl'))
</script>
