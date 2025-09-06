<template>
    <button
        @click="$emit('toggle')"
        class="relative p-2  cursor-pointer focus:outline-none rounded-md transition-all duration-200"
        :class="{ 'rotate-180': isOpen }"
    >
        <span class="w-6 h-6 flex flex-col justify-center space-y-1">
            <span
                class="block h-0.5 bg-zinc-900 transform transition-all duration-300 origin-center"
                :class="isOpen ? 'rotate-45 translate-y-1.5' : ''"
            ></span>
            <span
                class="block h-0.5 bg-zinc-900 transform transition-all duration-300"
                :class="isOpen ? 'opacity-0' : ''"
            ></span>
            <span
                class="block h-0.5 bg-zinc-900 transform transition-all duration-300 origin-center"
                :class="isOpen ? '-rotate-45 -translate-y-1.5' : ''"
            ></span>
        </span>
    </button>
</template>

<script setup>
defineProps({
    isOpen: { type: Boolean, required: true }
})

defineEmits(['toggle'])
</script>
