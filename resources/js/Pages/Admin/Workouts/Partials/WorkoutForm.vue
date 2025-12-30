<template>
    <div class="">
        <FormSection title="Workout Details" description="Enter the workout name and categories." :separator="false">
            <div class="space-y-4">
                <div>
                    <TextInput id="name" v-model="form.name" type="text" placeholder="Workout name"/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Categories *" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <label
                            v-for="cat in categories"
                            :key="cat"
                            class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 p-2 rounded"
                        >
                            <Checkbox
                                :value="cat"
                                :checked="form.categories"
                                @update:checked="form.categories = $event"
                            />
                            <span class="text-sm">{{ cat }}</span>
                        </label>
                    </div>
                    <InputError :message="form.errors.categories" />
                </div>
            </div>
        </FormSection>

        <FormButtons>
            <Link :href="route('admin.workouts.index')">
                <TransparentButton>Cancel</TransparentButton>
            </Link>

            <primary-button
                @click="saveWorkout"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                    {{ isEditing ? 'Update Workout' : 'Save Workout' }}
            </primary-button>
        </FormButtons>
    </div>
</template>

<script setup>
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import {Link} from '@inertiajs/vue3'
import { inject, computed } from 'vue'

import Checkbox from '@/Components/Checkbox.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import { scrollToFirstError } from '@/Components/Form/useScrollToError.js'

const form = inject('form')
const categories = inject('categories')
const workout = inject('workout', null)

const { route } = window

const isEditing = computed(() => workout !== null)

const saveWorkout = () => {
    const url = isEditing.value
        ? route('admin.workouts.update', { workout: workout.id })
        : route('admin.workouts.store')

    const method = isEditing.value ? 'put' : 'post'

    form[method](url, {
        onFinish: () => {
            // form.reset()
        },
        onError: () => scrollToFirstError(),
    })
}
</script>