<template>
    <div ref="headerRef" :class="[
        'w-full',
        sticky && 'sticky top-0 z-20 bg-white pt-4 lg:pt-6'
    ]">
        <div class="flex justify-between items-center w-full">
            <div class="text-2xl/8 font-bold w-full text-zinc-950 sm:text-xl/8 capitalize">
                <slot></slot>
            </div>
            <slot name="buttons"></slot>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    sticky: { type: Boolean, default: false },
})
</script>
