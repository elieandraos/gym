<template>
    <h2 class="font-bold text-zinc-950 capitalize">Cancel Session</h2>
    <hr class="border-t my-2">
    <div class=" text-zinc-500 my-8">Are you sure you want to cancel this session?</div>
    <div class="flex justify-end mt-8 gap-4">
        <TransparentButton @click="emit('close')">exit</TransparentButton>
        <primary-button @click="emit('cancel')">Yes, Cancel it</primary-button>
    </div>
</template>

<script setup>
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

const emit = defineEmits(['close', 'cancel'])
</script>
