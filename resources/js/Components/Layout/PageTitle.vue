<template>
    <div :class="['flex justify-between items-center', sticky && 'sticky top-0 z-10 bg-white -mt-6 pt-6']">
        <h2 class="text-2xl/8 font-bold text-zinc-950 sm:text-xl/8 grow capitalize">
            <slot></slot>
        </h2>
        <slot name="buttons"></slot>
    </div>
</template>

<script setup>
defineProps({
    sticky: { type: Boolean, default: false },
})
</script>
