<template>
    <div class="flex justify-between items-end">
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

        <div class="flex items-center gap-4">
            <Link :href="route('admin.trainers.edit', id)">
                <SecondaryButton>Edit Information</SecondaryButton>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'

const { route } = window

const props = defineProps({
    trainer: { type: Object, required: true },
    bordered: { type: Boolean, default: false },
})

const { id, name, profile_photo_url, age, since } = props.trainer
</script>
