<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap my-12 w-full justify-center space-x-2">
            <template v-for="(link, index) in links" :key="index">

                <div v-if="index === 0" class="w-10 h-10 flex items-center justify-center">
                    <ChevronLeftIcon
                        class="w-5 h-5 text-zinc-300 cursor-not-allowed"
                        v-if="link.url === null"/>
                    <Link v-if="link.url" :href="link.url">
                        <ChevronLeftIcon class="w-5 h-5 text-zinc-400" />
                    </Link>
                </div>

                <div v-if="index > 0 && index < (links.length -1)">
                    <Link
                        class="p-4 h-10 w-10 rounded-full text-zinc-400 bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center"
                        :class="{ 'text-zinc-500 bg-zinc-200 hover:bg-zinc-200': link.active }"
                        :href="link.url">
                        {{ link.label }}
                    </Link>
                </div>

                <div v-if="index === (links.length - 1)" class="w-10 h-10 flex items-center justify-center">
                    <ChevronRightIcon
                        class="w-5 h-5 text-zinc-300 cursor-not-allowed"
                        v-if="link.url === null"/>
                    <Link v-if="link.url" :href="link.url">
                        <ChevronRightIcon class="w-5 h-5 text-zinc-400" />
                    </Link>
                </div>

            </template>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3'

defineProps({
    links: { type: Array, required: true },
})
</script>
