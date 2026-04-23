<template>
    <Modal :show="show" @close="$emit('close')" max-width="md">
        <div class="space-y-4">
            <h2 class="text-lg font-medium text-zinc-900">Clone workout</h2>

            <div v-if="workout" class="text-sm text-zinc-600">
                Cloning <span class="font-medium text-zinc-900">{{ workout.name }}</span>
            </div>

            <div v-if="currentCircuits.length > 0" class="space-y-2">
                <p class="text-sm text-zinc-700 font-medium">Add to circuit:</p>
                <div class="space-y-2">
                    <label
                        v-for="circuit in currentCircuits"
                        :key="circuit.id"
                        class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                        :class="selectedCircuitId === circuit.id ? 'border-zinc-900 bg-zinc-50' : 'border-zinc-200 hover:border-zinc-300'"
                    >
                        <input
                            type="radio"
                            :value="circuit.id"
                            v-model="selectedCircuitId"
                            class="accent-zinc-900"
                        />
                        <span class="text-sm text-zinc-800">{{ circuit.name }}</span>
                    </label>
                    <label
                        class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                        :class="selectedCircuitId === null ? 'border-zinc-900 bg-zinc-50' : 'border-zinc-200 hover:border-zinc-300'"
                    >
                        <input
                            type="radio"
                            :value="null"
                            v-model="selectedCircuitId"
                            class="accent-zinc-900"
                        />
                        <span class="text-sm text-zinc-800">New circuit</span>
                    </label>
                </div>
            </div>

            <div v-else class="text-sm text-zinc-500">
                A new circuit will be created for this workout.
            </div>
        </div>

        <template #footer>
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50 sm:w-auto cursor-pointer transition ease-in-out duration-150"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="submit"
                    :disabled="processing"
                    class="inline-flex w-full justify-center rounded-md bg-zinc-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 active:bg-zinc-950 sm:w-auto cursor-pointer transition ease-in-out duration-150 disabled:opacity-50"
                >
                    {{ processing ? 'Cloning...' : 'Clone workout' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Layout/Modal.vue'

const props = defineProps({
    show: { type: Boolean, required: true },
    workout: { type: Object, default: null },
    currentCircuits: { type: Array, default: () => [] },
    bookingSlotId: { type: Number, required: true },
})

const emit = defineEmits(['close', 'cloned'])

const selectedCircuitId = ref(null)
const processing = ref(false)

// Default to first circuit when opening, if any exist
watch(() => props.show, (newValue) => {
    if (newValue && props.currentCircuits.length > 0) {
        selectedCircuitId.value = props.currentCircuits[0].id
    } else {
        selectedCircuitId.value = null
    }
})

const submit = () => {
    if (!props.workout) {
        return
    }

    processing.value = true

    router.post(
        route('admin.bookings-slots.clone-circuit-workout', { bookingSlot: props.bookingSlotId }),
        {
            source_workout_id: props.workout.id,
            circuit_id: selectedCircuitId.value,
        },
        {
            onSuccess: () => {
                emit('cloned')
                emit('close')
            },
            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>
