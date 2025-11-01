<template>
    <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
        <div class="space-y-1">
            <h2 class="font-[600]">
                {{ title }}
            </h2>
            <p class="text-[#71717b]">
                {{  description }}
            </p>
        </div>
        <div>
            <slot></slot>
        </div>
    </section>
    <hr v-if="separator" class="my-10 w-full border-t border-zinc-100" />
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, required: false, default: '' },
    separator: { type: Boolean, required: false, default: true },
})
</script>
