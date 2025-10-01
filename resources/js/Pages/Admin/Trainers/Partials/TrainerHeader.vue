<template>
    <div class="flex justify-between items-center">
        <div class="flex flex-wrap grow items-center gap-4">
            <PageBackButton />
            <div class="flex gap-2 items-center">
                <img class="size-12 flex-shrink-0 rounded-full object-cover" :src="profile_photo_url" :alt="name">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[20px] font-[600] leading-[32px]">{{ name }}</h1>
                    </div>
                    <div class="text-[#71717b]">{{ age }} years old · joined on {{  since }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import PageBackButton from '@/Components/Layout/PageBackButton.vue'

const props = defineProps({
    trainer: { type: Object, required: true },
    bordered: { type: Boolean, default: false },
})

const { name, profile_photo_url, age, since } = props.trainer
</script>
