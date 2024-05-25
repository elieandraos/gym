<template>
    <div class="lg:mt-4 mb-8">
        <div class="inline-flex gap-1 items-center group cursor-pointer" @click="goTo">
            <chevron-left-icon class="w-3 h-3 text-zinc-400 group-hover:text-zinc-500"></chevron-left-icon>
            <span class="inline-flex text-zinc-400 text-xs font-medium leading-relaxed group-hover:text-zinc-500">
                <slot></slot>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    url: { type: String, required: false },
})

const goTo = () => (props.url ? router.visit(props.url) : history.back())
</script>
