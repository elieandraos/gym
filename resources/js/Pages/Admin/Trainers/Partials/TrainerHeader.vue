<template>
    <div class="flex justify-between items-center pb-4" :class="{ 'mb-12 border-b border-zinc-200': bordered }">
        <div class="flex flex-wrap grow items-center gap-4">
            <img class="h-16 w-16 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl/8 font-bold text-zinc-950">{{ name }}</h1>
                </div>
                <div class="mt-1 text-sm text-zinc-500">{{ age }} years old · Member since {{  since }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    trainer: { type: Object, required: true },
    bordered: { type: Boolean, default: false },
})

const { name, profile_photo_url, age, since } = props.trainer
</script>
