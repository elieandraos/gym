<template>
    <div class="space-y-4">
        <div
            class="min-h-40 p-4 bg-stone-50 border border-stone-100 rounded-lg"
            @dragover.prevent
            @drop="drop"
        >
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div
                    v-for="(selectedWorkout, workoutIndex) in selectedWorkouts"
                    :key="selectedWorkout.id"
                    class="bg-white p-4 rounded-lg space-y-3"
                >
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">{{ selectedWorkout.name }}</span>
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    @click="addSet(workoutIndex)"
                                    class="text-gray-400 hover:text-indigo-500 p-1 cursor-pointer hover:bg-indigo-50 rounded"
                                    title="Add set"
                                >
                                    <PlusIcon class="w-4 h-4" />
                                </button>
                                <button
                                    type="button"
                                    @click="removeSet(workoutIndex)"
                                    class="text-gray-400 hover:text-red-500 p-1 cursor-pointer hover:bg-red-50 rounded"
                                    :disabled="selectedWorkout.weight_in_kg.length <= 1"
                                    :class="{ 'opacity-50 cursor-not-allowed': selectedWorkout.weight_in_kg.length <= 1 }"
                                    title="Remove set"
                                >
                                    <MinusIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="remove(workoutIndex)" class="text-red-300 hover:text-red-500 p-1.5 cursor-pointer hover:bg-red-50 hover:rounded-lg">
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-1 text-[#71717b]">
                            <input
                                type="radio"
                                :name="'type-' + workoutIndex"
                                value="weight"
                                v-model="selectedWorkout.type"
                            />
                            Weight
                        </label>
                        <label class="flex items-center gap-1 text-[#71717b]">
                            <input
                                type="radio"
                                :name="'type-' + workoutIndex"
                                value="seconds"
                                v-model="selectedWorkout.type"
                            />
                            Seconds
                        </label>
                    </div>

                    <div v-if="selectedWorkout.type === 'weight'" class="space-y-2">
                        <div class="flex items-center gap-2" v-for="(value, idx) in selectedWorkout.weight_in_kg" :key="idx">
                            <TextInput
                                v-model="selectedWorkout.weight_in_kg[idx]"
                                name="weight_in_kg[]"
                                type="number"
                                placeholder="KG"
                                class="flex-1"
                            />
                            <span class="text-gray-500">×</span>
                            <TextInput
                                v-model="selectedWorkout.reps[idx]"
                                name="reps[]"
                                type="number"
                                placeholder="Iterations"
                                class="flex-1"
                            />
                        </div>
                    </div>

                    <div v-if="selectedWorkout.type === 'seconds'" class="space-y-2">
                        <div v-for="(value, idx) in selectedWorkout.duration_in_seconds" :key="idx">
                            <TextInput
                                v-model="selectedWorkout.duration_in_seconds[idx]"
                                name="duration_in_seconds[]"
                                type="number"
                                placeholder="Seconds"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right space-x-4">
            <Link :href="route('admin.bookings-slots.show', bookingSlotId)">
                <TransparentButton>Cancel</TransparentButton>
            </Link>
            <PrimaryButton @click="saveWorkouts" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save Workouts
            </PrimaryButton>
        </div>
    </div>
</template>

<script setup>
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import { TrashIcon, PlusIcon, MinusIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3'
import { inject } from 'vue'

const { selectedWorkouts, drop, remove, saveWorkouts, form, bookingSlotId } = inject('workoutState')
const { route } = window

const addSet = (workoutIndex) => {
    const workout = selectedWorkouts.value[workoutIndex]

    if (workout.type === 'weight') {
        workout.weight_in_kg.push('')
        workout.reps.push('12')
    } else {
        workout.reps.push('1')
    }

    workout.duration_in_seconds.push('')
}

const removeSet = (workoutIndex) => {
    const workout = selectedWorkouts.value[workoutIndex]

    if (workout.weight_in_kg.length > 1) {
        workout.weight_in_kg.pop()
        workout.reps.pop()
        workout.duration_in_seconds.pop()
    }
}
</script>
