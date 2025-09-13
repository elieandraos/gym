<template>
    <Link :href="href" :class="classes" class="flex items-center gap-1 px-3 py-2.5 transition duration-150 ease-in-out rounded-lg group hover:bg-zinc-200 hover:bg-opacity-30">
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    href: String,
    active: Boolean,
})

const classes = computed(() => (props.active
    ? 'bg-zinc-200 bg-opacity-80'
    : 'hover:text-zinc-900'))
</script>
