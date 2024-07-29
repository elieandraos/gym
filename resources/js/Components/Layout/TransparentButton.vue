<template>
    <button :type="type" class="inline-flex items-center justify-center px-4 py-2.5 bg-transparent border border-transparent rounded-md font-semibold text-xs text-zinc-500 uppercase tracking-widest hover:bg-zinc-100 focus:outline-none transition ease-in-out duration-150">
        <slot />
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
})
</script>
