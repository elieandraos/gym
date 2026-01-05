<template>
    <Modal :show="show" @close="handleClose" max-width="7xl">
        <div class="space-y-6">
            <!-- Title -->
            <h2 class="text-lg font-medium text-zinc-900">Previous Sessions</h2>
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-zinc-900"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!previousSessions || previousSessions.length === 0" class="text-center py-12 text-zinc-500">
                No previous sessions found
            </div>

            <!-- Sessions List -->
            <div v-else class="space-y-8">
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
                        />
                    </div>

                    <!-- Empty State for Session -->
                    <div v-else class="text-center py-8 text-zinc-400 text-sm">
                        No circuits recorded for this session
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Layout/Modal.vue'
import ReadOnlyCircuitColumn from './ReadOnlyCircuitColumn.vue'

const props = defineProps({
    show: { type: Boolean, required: true },
    bookingSlotId: { type: Number, required: true },
    limit: { type: Number, default: 2 },
})

const emit = defineEmits(['close'])

const loading = ref(false)
const previousSessions = ref([])

const fetchPreviousSessions = async () => {
    loading.value = true
    try {
        const response = await fetch(
            route('admin.bookings-slots.circuit-workout-history', {
                bookingSlot: props.bookingSlotId,
                limit: props.limit,
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
    }
}

const handleClose = () => {
    emit('close')
}

// Fetch data when modal opens
watch(() => props.show, (newValue) => {
    if (newValue) {
        fetchPreviousSessions()
    }
})
</script>