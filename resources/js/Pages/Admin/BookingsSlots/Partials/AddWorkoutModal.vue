<template>
    <Modal :show="show" max-width="2xl" @close="close">
        <div class="space-y-6 p-2">
            <!-- Workout Selection -->
            <div>
                <InputAutocomplete
                    v-model="selectedWorkoutId"
                    :options="workoutOptions"
                    placeholder="Search workouts..."
                />
                <InputError v-if="errors.workout" :message="errors.workout" class="mt-1" />
            </div>

            <!-- Type Selection -->
            <div>
                <div class="flex gap-4 mt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            v-model="type"
                            value="weight"
                            class="h-4 w-4 border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm text-zinc-700 cursor-pointer">Weight</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            v-model="type"
                            value="duration"
                            class="h-4 w-4 border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm text-zinc-700 cursor-pointer">Duration</span>
                    </label>
                </div>
            </div>

            <!-- Sets Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <button
                        @click="addSet"
                        type="button"
                        class="text-sm text-indigo-600 hover:text-indigo-700 flex items-center gap-1 cursor-pointer ml-auto"
                    >
                        <PlusIcon class="w-4 h-4 cursor-pointer" />
                        Add Set
                    </button>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(set, index) in sets"
                        :key="index"
                        class="flex gap-3 items-center"
                    >
                        <!-- Weight Type Fields -->
                        <template v-if="type === 'weight'">
                            <div class="flex-1 flex items-center gap-2">
                                <TextInput
                                    v-model="set.reps"
                                    type="number"
                                    min="1"
                                    class="flex-1"
                                />
                                <label class="text-sm text-zinc-600 whitespace-nowrap">Reps</label>
                            </div>
                            <div class="flex-1 flex items-center gap-2">
                                <TextInput
                                    v-model="set.weight_in_kg"
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    class="flex-1"
                                />
                                <label class="text-sm text-zinc-600 whitespace-nowrap">Weight (kg)</label>
                            </div>
                        </template>

                        <!-- Duration Type Fields -->
                        <template v-else>
                            <div class="flex-1 flex items-center gap-2">
                                <TextInput
                                    v-model="set.duration_in_seconds"
                                    type="number"
                                    min="1"
                                    class="flex-1"
                                />
                                <label class="text-sm text-zinc-600 whitespace-nowrap">Duration (seconds)</label>
                            </div>
                        </template>

                        <!-- Remove Set Button -->
                        <button
                            v-if="sets.length > 1"
                            @click="removeSet(index)"
                            type="button"
                            class="text-zinc-400 hover:text-red-500 cursor-pointer"
                        >
                            <MinusCircleIcon class="w-5 h-5 cursor-pointer" />
                        </button>
                    </div>
                </div>
                <InputError v-if="errors.sets" :message="errors.sets" class="mt-1" />
            </div>
        </div>

        <template #footer>
            <div class="flex gap-2 justify-end">
                <SecondaryButton @click="close">Cancel</SecondaryButton>
                <PrimaryButton @click="submit">
                    {{ editingWorkout ? 'Update Workout' : 'Add Workout' }}
                </PrimaryButton>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { PlusIcon, MinusCircleIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Layout/Modal.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import InputAutocomplete from '@/Components/Form/InputAutocomplete.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import InputError from '@/Components/Form/InputError.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    circuitId: { type: Number, required: true },
    bookingSlotId: { type: Number, required: true },
    availableWorkouts: { type: Array, required: true },
    editingWorkout: { type: Object, default: null },
})

const emit = defineEmits(['close'])

// Form state
const selectedWorkoutId = ref(null)
const type = ref('weight')
const sets = ref([
    { reps: 12, weight_in_kg: null, duration_in_seconds: null },
    { reps: 12, weight_in_kg: null, duration_in_seconds: null },
    { reps: 12, weight_in_kg: null, duration_in_seconds: null },
])
const errors = ref({})

// Transform workouts for InputAutocomplete
const workoutOptions = computed(() => {
    return props.availableWorkouts.map(w => ({
        value: w.id,
        label: w.name,
    }))
})

// Get selected workout details
const selectedWorkout = computed(() => {
    return props.availableWorkouts.find(w => w.id === selectedWorkoutId.value)
})

// Watch for editing workout changes
watch(() => props.editingWorkout, (workout) => {
    if (workout) {
        // Find the workout ID from available workouts by matching the name
        const matchingWorkout = props.availableWorkouts.find(w => w.name === workout.name)
        selectedWorkoutId.value = matchingWorkout?.id || null

        // Determine type from first set
        if (workout.sets && workout.sets.length > 0) {
            type.value = workout.sets[0].weight_in_kg !== null ? 'weight' : 'duration'
        }
        // Load sets
        sets.value = workout.sets && workout.sets.length > 0
            ? workout.sets.map(set => ({ ...set }))
            : [{ reps: 12, weight_in_kg: null, duration_in_seconds: null }]
    }
}, { immediate: true })

// Watch type changes and reset sets (only if not editing)
watch(type, (newType) => {
    if (!props.editingWorkout) {
        sets.value = sets.value.map(() => ({
            reps: newType === 'weight' ? 12 : null,
            weight_in_kg: newType === 'weight' ? null : null,
            duration_in_seconds: newType === 'duration' ? null : null,
        }))
    }
})

const addSet = () => {
    if (sets.value.length < 10) {
        sets.value.push({
            reps: type.value === 'weight' ? 12 : null,
            weight_in_kg: type.value === 'weight' ? null : null,
            duration_in_seconds: type.value === 'duration' ? null : null,
        })
    }
}

const removeSet = (index) => {
    if (sets.value.length > 1) {
        sets.value.splice(index, 1)
    }
}

const validate = () => {
    errors.value = {}

    if (!selectedWorkoutId.value) {
        errors.value.workout = 'Please select a workout'
    }

    if (sets.value.length === 0) {
        errors.value.sets = 'At least one set is required'
    }

    // Validate sets have values
    const hasEmptySets = sets.value.some(set => {
        if (type.value === 'weight') {
            return !set.reps || !set.weight_in_kg
        } else {
            return !set.duration_in_seconds
        }
    })

    if (hasEmptySets) {
        errors.value.sets = 'All sets must have values'
    }

    return Object.keys(errors.value).length === 0
}

const submit = () => {
    if (!validate()) {
        return
    }

    const workoutData = {
        workout_id: selectedWorkoutId.value,
        type: type.value,
        sets: sets.value.map(set => ({
            reps: type.value === 'weight' ? parseInt(set.reps) : null,
            weight_in_kg: type.value === 'weight' ? parseFloat(set.weight_in_kg) : null,
            duration_in_seconds: type.value === 'duration' ? parseInt(set.duration_in_seconds) : null,
        }))
    }

    // Check if we're editing or creating
    const isEditing = props.editingWorkout !== null

    if (isEditing) {
        // Update existing workout
        router.put(
            route('admin.bookings-slots.circuits.workouts.update', {
                bookingSlot: props.bookingSlotId,
                circuit: props.circuitId,
                circuitWorkout: props.editingWorkout.id,
            }),
            workoutData,
            {
                preserveScroll: true,
                onSuccess: () => {
                    resetForm()
                    emit('close')
                },
                onError: (serverErrors) => {
                    errors.value = serverErrors
                },
            }
        )
    } else {
        // Create new workout
        router.post(
            route('admin.bookings-slots.circuits.workouts.store', {
                bookingSlot: props.bookingSlotId,
                circuit: props.circuitId,
            }),
            workoutData,
            {
                preserveScroll: true,
                onSuccess: () => {
                    resetForm()
                    emit('close')
                },
                onError: (serverErrors) => {
                    errors.value = serverErrors
                },
            }
        )
    }
}

const resetForm = () => {
    selectedWorkoutId.value = null
    type.value = 'weight'
    sets.value = [
        { reps: 12, weight_in_kg: null, duration_in_seconds: null },
        { reps: 12, weight_in_kg: null, duration_in_seconds: null },
        { reps: 12, weight_in_kg: null, duration_in_seconds: null },
    ]
    errors.value = {}
}

const close = () => {
    resetForm()
    emit('close')
}
</script>
