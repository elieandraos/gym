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
                        <span class="font-semibold">{{ selectedWorkout.name }}</span>
                        <button type="button" @click="remove(workoutIndex)" class="text-red-700 hover:text-red-500 p-2 cursor-pointer hover:bg-red-50 hover:rounded-lg">
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex items-center gap-4 text-sm">
                        <label class="flex items-center gap-1">
                            <input
                                type="radio"
                                :name="'type-' + workoutIndex"
                                value="weight"
                                v-model="selectedWorkout.type"
                            />
                            Weight
                        </label>
                        <label class="flex items-center gap-1">
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
                                placeholder="Reps"
                                class="flex-1"
                            />
                        </div>
                    </div>

                    <div v-if="selectedWorkout.type === 'seconds'" class="flex gap-2">
                        <TextInput
                            v-for="(value, idx) in selectedWorkout.duration_in_seconds"
                            :key="idx"
                            v-model="selectedWorkout.duration_in_seconds[idx]"
                            name="duration_in_seconds[]"
                            type="number"
                            placeholder="Seconds"
                            class="w-16"
                        />
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
import { TrashIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3'
import { inject } from 'vue'

const { selectedWorkouts, drop, remove, saveWorkouts, form, bookingSlotId } = inject('workoutState')
const { route } = window
</script>
