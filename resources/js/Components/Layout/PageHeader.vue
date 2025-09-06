<template>
    <div ref="headerRef" :class="[
        'w-full pb-4',
        sticky && 'sticky top-0 z-20 bg-white',
        sticky && '-mt-6 lg:mt-0 pt-6 lg:pt-6',
        bordered && 'border-b border-zinc-200',
        bottomGap && 'mb-8'
    ]">
        <div class="flex justify-between items-center w-full">
            <div class="w-full"><slot></slot></div>
            <slot name="buttons"></slot>
        </div>
    </div>
    <!-- Automatic spacer for sticky headers on mobile -->
    <div v-if="sticky" class="pt-6 lg:pt-0"></div>
</template>

<script setup>
const props = defineProps({
    sticky: { type: Boolean, default: false },
    bordered: { type: Boolean, default: true },
    bottomGap: { type: Boolean, default: true },
})
</script>
