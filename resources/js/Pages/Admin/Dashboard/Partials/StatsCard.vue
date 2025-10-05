<template>
    <div class="bg-white rounded-lg p-6">
        <div v-if="loading" class="animate-pulse">
            <div class="flex items-center gap-4">
                <div class="size-12 bg-zinc-100 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-zinc-100 rounded w-24"></div>
                    <div class="h-8 bg-zinc-100 rounded w-16"></div>
                </div>
            </div>
        </div>

        <div v-else class="flex items-center gap-4">
            <div class="flex-shrink-0">
                <slot name="icon">
                    <div class="size-12 bg-zinc-100 rounded-lg flex items-center justify-center">
                        <component :is="icon" class="size-6 text-zinc-400" />
                    </div>
                </slot>
            </div>
            <div class="flex-1">
                <p class="text-sm text-zinc-500 mb-1">{{ title }}</p>
                <p class="text-3xl font-[600] text-zinc-900">{{ value }}</p>
                <p v-if="subtitle" class="text-xs text-zinc-400 mt-1">{{ subtitle }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], default: 0 },
    subtitle: { type: String, default: '' },
    icon: { type: Object, default: null },
    loading: { type: Boolean, default: false },
})
</script>
