<template>
    <div :class="['w-full pt-0', sticky && 'sticky top-0 z-20 lg:-mt-6 lg:pt-6 bg-white ']">
        <div class="flex justify-between items-center w-full">
            <div class="text-2xl/8 font-bold w-full text-zinc-950 sm:text-xl/8 capitalize">
                <slot></slot>
            </div>
            <slot name="buttons"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    sticky: { type: Boolean, default: false },
})
</script>
