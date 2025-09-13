<template>
    <button :type="type" class="inline-flex items-center justify-center px-[11px] py-[5px] bg-transparent border border-transparent rounded-md font-[500] text-zinc-500 capitalize tracking-widest cursor-pointer hover:bg-zinc-100 focus:outline-none transition ease-in-out duration-150">
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
