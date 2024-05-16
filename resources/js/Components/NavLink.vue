<template>
    <Link :href="href" :class="classes" class="flex items-center px-3 py-2 font-medium transition duration-150 ease-in-out group">
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
    ? 'text-zinc-900'
    : 'text-zinc-500  hover:text-zinc-900'))
</script>
