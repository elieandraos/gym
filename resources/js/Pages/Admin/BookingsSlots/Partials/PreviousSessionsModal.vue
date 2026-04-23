<template>
    <Modal :show="show" @close="handleClose" max-width="7xl">
        <div class="space-y-6">
            <!-- Header with title and selector -->
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-zinc-900">Previous Sessions</h2>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-zinc-600">Show</label>
                    <select
                        v-model="selectedLimit"
                        class="text-sm border-zinc-200 rounded-md focus:border-zinc-400 focus:ring-zinc-400 py-1 px-2"
                    >
                        <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                    </select>
                    <span class="text-sm text-zinc-600">sessions</span>
                </div>
            </div>
            <!-- Content Container -->
            <div ref="contentContainer" class="relative" :style="containerStyle">
                <!-- Loading Overlay -->
                <div v-if="loading" class="absolute inset-0 flex justify-center items-center bg-white/80 z-10">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-zinc-900"></div>
                </div>

                <!-- Empty State -->
                <div v-if="!loading && (!previousSessions || previousSessions.length === 0)" class="flex justify-center items-center py-12 text-zinc-500">
                    No previous sessions found
                </div>

                <!-- Sessions List -->
                <div v-else-if="!loading" class="space-y-8">
                    <div
                        v-for="session in previousSessions"
                        :key="session.id"
                        class="border border-zinc-200 rounded-lg p-4 bg-zinc-50"
                    >
                        <!-- Session Header -->
                        <div class="mb-4 pb-3 border-b border-zinc-200">
                            <div class="flex items-center gap-2 text-sm">
                                <CalendarIcon class="w-4 h-4 text-zinc-500" />
                                <span class="font-medium text-zinc-900">{{ session.formatted_date }}</span>
                                <span class="text-zinc-500">·</span>
                                <ClockIcon class="w-4 h-4 text-zinc-500" />
                                <span class="text-zinc-600">{{ session.start_time }}</span>
                            </div>
                        </div>

                        <!-- Circuits (Horizontal Scroll) -->
                        <div v-if="session.circuits && session.circuits.length > 0"
                             class="flex gap-4 overflow-x-auto pb-2">
                            <ReadOnlyCircuitColumn
                                v-for="circuit in session.circuits"
                                :key="circuit.id"
                                :circuit="circuit"
                                @clone-circuit="handleCloneCircuit"
                                @clone-workout="handleCloneWorkout"
                            />
                        </div>

                        <!-- Empty State for Session -->
                        <div v-else class="text-center py-8 text-zinc-400 text-sm">
                            No circuits recorded for this session
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>

    <CloneWorkoutModal
        :show="showCloneWorkoutModal"
        :workout="workoutToClone"
        :current-circuits="currentCircuits"
        :booking-slot-id="bookingSlotId"
        @close="showCloneWorkoutModal = false"
        @cloned="handleClose"
    />
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Layout/Modal.vue'
import ReadOnlyCircuitColumn from './ReadOnlyCircuitColumn.vue'
import CloneWorkoutModal from './CloneWorkoutModal.vue'

const props = defineProps({
    show: { type: Boolean, required: true },
    bookingSlotId: { type: Number, required: true },
    limit: { type: Number, default: 2 },
    currentCircuits: { type: Array, default: () => [] },
})

const emit = defineEmits(['close'])

const loading = ref(false)
const previousSessions = ref([])
const selectedLimit = ref(3)
const contentContainer = ref(null)
const lockedHeight = ref(null)
const showCloneWorkoutModal = ref(false)
const workoutToClone = ref(null)

const containerStyle = computed(() => {
    return lockedHeight.value ? { minHeight: `${lockedHeight.value}px` } : {}
})

const fetchPreviousSessions = async () => {
    // Lock the height before loading to prevent modal from shrinking
    if (contentContainer.value) {
        lockedHeight.value = contentContainer.value.offsetHeight
    }

    loading.value = true
    try {
        const response = await fetch(
            route('admin.bookings-slots.circuit-workout-history', {
                bookingSlot: props.bookingSlotId,
                limit: selectedLimit.value,
            })
        )
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`)
        }
        const data = await response.json()
        previousSessions.value = data.previousSessions
    } catch (error) {
        console.error('Failed to fetch previous sessions:', error)
        previousSessions.value = []
    } finally {
        loading.value = false
        lockedHeight.value = null
    }
}

const handleClose = () => {
    emit('close')
}

const handleCloneCircuit = (circuit) => {
    router.post(
        route('admin.bookings-slots.clone-circuit', { bookingSlot: props.bookingSlotId }),
        { source_circuit_id: circuit.id },
        { onSuccess: () => handleClose() }
    )
}

const handleCloneWorkout = (workout) => {
    workoutToClone.value = workout
    showCloneWorkoutModal.value = true
}

// Fetch data when modal opens
watch(() => props.show, (newValue) => {
    if (newValue) {
        fetchPreviousSessions()
    }
})

// Refetch when limit changes
watch(selectedLimit, () => {
    if (props.show) {
        fetchPreviousSessions()
    }
})
</script>
