<template>
    <div>
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 mx-5 sm:mx-0 md:mt-0 md:col-span-2">
            <div class="px-4 py-5 sm:p-6 bg-white shadow rounded-lg">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>

<script setup>
import SectionTitle from './SectionTitle.vue'
</script>
