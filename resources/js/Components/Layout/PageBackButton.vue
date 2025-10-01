<template>
    <button @click="goBack" class="flex-shrink-0 p-2 -ml-2 cursor-pointer bg-zinc-100 hover:bg-zinc-200 rounded-lg transition-colors">
        <ChevronLeftIcon class="w-5 h-5 text-zinc-600" />
    </button>
</template>

<script setup>
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const goBack = () => window.history.back()
</script>
