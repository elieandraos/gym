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
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="px-4 py-5 bg-white sm:p-6 shadow-lg rounded-t-lg"
                    :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'">
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form" />
                    </div>
                </div>

                <div v-if="hasActions" class="flex items-center justify-end px-4 py-3 bg-gray-50 text-end sm:px-6 shadow rounded-b-lg">
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'

import SectionTitle from './SectionTitle.vue'

defineEmits(['submitted'])

const hasActions = computed(() => !!useSlots().actions)
</script>
