<template>
    <div class="">
        <FormSection title="Workout Details" description="Enter the workout name and category." >
            <div class="space-y-2">
                <div>
                    <TextInput id="name" v-model="form.name" type="text" placeholder="Workout name"/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <SelectInput v-model="form.category" id="category" :options="categoryOptions" placeholder="Select category"/>
                    <InputError :message="form.errors.category" />
                </div>
            </div>
        </FormSection>

        <div class="text-right">
            <Link :href="route('admin.workouts.index')" class="mr-4">
                <TransparentButton>Cancel</TransparentButton>
            </Link>

            <primary-button
                @click="saveWorkout"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                    {{ isEditing ? 'Update Workout' : 'Save Workout' }}
            </primary-button>
        </div>
    </div>
</template>

<script setup>
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import {Link} from '@inertiajs/vue3'
import { inject, nextTick, computed } from 'vue'

import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'

const form = inject('form')
const categories = inject('categories')
const workout = inject('workout', null)

const { route } = window

const isEditing = computed(() => workout !== null)

const categoryOptions = computed(() => 
    categories?.map(category => ({ value: category, name: category })) || []
)

const scrollToFirstError = () => {
    nextTick(() => {
        const field = Object.keys(form.errors)[0]
        if (field) {
            document.getElementById(field)?.scrollIntoView({ behavior: 'smooth' })
        }
    })
}

const saveWorkout = () => {
    const url = isEditing.value 
        ? route('admin.workouts.update', { workout: workout.id })
        : route('admin.workouts.store')
    
    const method = isEditing.value ? 'put' : 'post'
    
    form[method](url, {
        preserveScroll: true,
        onFinish: () => {
            // form.reset()
        },
        onError: () => scrollToFirstError(),
    })
}
</script>