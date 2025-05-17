<template>
    <div class="px-1 py-0.5 w-13 bg-zinc-100 border border-zinc-200 rounded-full cursor-pointer" @click="model = !model">
        <div
            :class="{'translate-x-5 bg-zinc-800': model, 'bg-white': !model}"
            class="transform transition-transform w-4 h-4 rounded-full">
        </div>
    </div>
</template>

<script setup>
const model = defineModel()
</script>

<style scoped>
.transform {
    transition: transform 0.2s ease-in-out;
}
.translate-x-5 {
    transform: translateX(.5rem); /* Move the switch to the right */
}
</style>
