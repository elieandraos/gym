<template>
    <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
        <div class="space-y-1">
            <h2 class="font-bold text-zinc-950 sm:text-sm">
                {{ title }}
            </h2>
            <p class="text-zinc-500 sm:text-sm">
                {{  description }}
            </p>
        </div>
        <div>
            <slot></slot>
        </div>
    </section>
    <hr class="my-10 w-full border-t border-zinc-100" />
</template>

<script setup>
defineProps({
    title: { type: String, required: true},
    description: { type: String, required: false, default: ''},
    separator: { type: Boolean, required: false, default: true }
})
</script>
