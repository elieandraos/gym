<template>
    <div class="">
        <FormSection v-if="isEdit" title="Profile Photo" description="Upload a profile photo for the trainer.">
            <InputPhotoUpload
                :photo_url="profilePhotoUrl"
                :photo_path="profilePhotoPath"
                :name="form.name"
                @upload="(file) => { form.photo = file; form.remove_photo = false; }"
                @remove="() => { form.photo = null; form.remove_photo = true; }" />
            <InputError :message="form.errors.photo" />
        </FormSection>

        <FormSection title="Registration" description="Enter the starting date of the trainer." >
            <DateInput v-model="form.registration_date"></DateInput>
            <InputError :message="form.errors.registration_date" />
        </FormSection>

        <FormSection title="Profile" description="Enter the trainer's name and gender." >
            <div class="space-y-2">
                <div>
                    <TextInput id="name" v-model="form.name" type="text" placeholder="Name"/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <SelectInput v-model="form.gender" id="gender" :options="$page.props.genders" placeholder="Select gender"/>
                    <InputError :message="form.errors.gender" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Age" description="Enter the trainer's birthdate" >
            <div class="space-y-2">
                <div>
                    <DateInput v-model="form.birthdate"></DateInput>
                    <InputError :message="form.errors.birthdate" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Body information" description="Enter the trainer's weight, height and blood type." >
            <div class="space-y-2">
                <div>
                    <TextInput id="height" v-model="form.height" type="text" placeholder="Height in cm"/>
                    <InputError :message="form.errors.height" />
                </div>
                <div>
                    <TextInput id="weight" v-model="form.weight" type="text" placeholder="Weight in kg" />
                    <InputError :message="form.errors.weight" />
                </div>
                <div>
                    <SelectInput v-model="form.blood_type" id="blood_type" :options="$page.props.bloodTypes" placeholder="Select blood type"/>
                    <InputError :message="form.errors.blood_type" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Contact" description="Enter the trainer's email, phone and other contact information." >
            <div class="space-y-2">
                <div>
                    <TextInput id="email" v-model="form.email" type="text" placeholder="Email"/>
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <TextInput id="phone_number" v-model="form.phone_number" type="text" placeholder="Phone number"/>
                    <InputError :message="form.errors.phone_number" placeholder="Phone number"/>
                </div>
                <div>
                    <TextInput id="address" v-model="form.address" type="text" placeholder="Location"/>
                    <InputError :message="form.errors.address" />
                </div>
                <div>
                    <TextInput id="emergency_contact" v-model="form.emergency_contact" type="text" placeholder="Emergency Contact"/>
                    <InputError :message="form.errors.emergency_contact" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Social" description="Enter the trainer's instagram handle." >
            <TextInput id="instagram_handle" v-model="form.instagram_handle" type="text" placeholder="Instagram handle"/>
            <InputError :message="form.errors.instagram_handle" />
        </FormSection>

        <FormSection title="Calendar Color" description="Choose the color that will appear on the calendar for this trainer." >
            <InlineColorPicker v-model="form.color" />
            <InputError :message="form.errors.color" />
        </FormSection>

        <div class="text-right">
            <Link :href="route('admin.trainers.index')" class="mr-4">
                <TransparentButton>Cancel</TransparentButton>
            </Link>

            <primary-button
                @click="saveUser"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                    {{ buttonText }}
            </primary-button>
        </div>
    </div>
</template>

<script setup>
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import {Link} from '@inertiajs/vue3'
import { inject, computed } from 'vue'

import DateInput from '@/Components/Form/DateInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InlineColorPicker from '@/Components/Form/InlineColorPicker.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputPhotoUpload from '@/Components/Form/InputPhotoUpload.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import { scrollToFirstError } from '@/Components/Form/useScrollToError.js'

const props = defineProps({
    isEdit: { type: Boolean, default: false },
})

const form = inject('form')
const userId = inject('userId', null)
const profilePhotoUrl = inject('profilePhotoUrl', '')
const profilePhotoPath = inject('profilePhotoPath', '')

const { route } = window

const saveUser = () => {
    const routeName = props.isEdit ? 'admin.trainers.update' : 'admin.trainers.store'
    const routeParams = props.isEdit ? [userId] : []

    if (props.isEdit) {
        // For updates, use POST with _method spoofing to support file uploads
        form.post(route(routeName, ...routeParams), {
            _method: 'put',
            onFinish: () => {
                // form.reset()
            },
            onError: () => scrollToFirstError(),
        })
    } else {
        form.post(route(routeName, ...routeParams), {
            onFinish: () => {
                // form.reset()
            },
            onError: () => scrollToFirstError(),
        })
    }
}

const buttonText = computed(() => props.isEdit ? 'Update Trainer' : 'Save Trainer')
</script>
