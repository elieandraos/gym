<template>
    <button
        @click="addCircuit"
        class="w-full md:w-[30%] md:min-w-[300px] md:flex-shrink-0 border-2 border-dashed border-zinc-300 rounded-lg p-3 text-zinc-400 hover:border-zinc-400 hover:text-zinc-500 transition-colors flex items-center justify-center gap-2 cursor-pointer"
    >
        <PlusIcon class="w-5 h-5 cursor-pointer" />
        <span>Add Circuit</span>
    </button>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline'

defineProps({
    bookingSlotId: { type: Number, required: true },
})

const emit = defineEmits(['circuit-added'])

// Generate unique ID for dummy circuits
let circuitCounter = 1

const addCircuit = () => {
    const newCircuit = {
        id: Date.now(), // Use timestamp as unique ID
        name: `Circuit ${circuitCounter}`,
        workouts: []
    }

    circuitCounter++
    emit('circuit-added', newCircuit)
}
</script>
