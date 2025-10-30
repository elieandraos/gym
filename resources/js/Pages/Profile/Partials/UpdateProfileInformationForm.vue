<template>
    <form @submit.prevent="emits('save-user-info')">
        <FormSection title="Profile Photo" description="Upload a profile photo for your account.">
            <InputPhotoUpload
                @upload="handleUpload"
                @remove="deletePhoto"
                :photo_url="$page.props.auth.user.profile_photo_url"
                :photo_path="$page.props.auth.user.profile_photo_path"
                :name="form.name">
            </InputPhotoUpload>
            <InputError :message="form.errors.photo" />
        </FormSection>

        <FormSection title="Personal Information" description="Enter your name, gender, and birthdate.">
            <div class="space-y-2">
                <div>
                    <TextInput id="name" v-model="form.name" type="text" placeholder="Name"/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <SelectInput v-model="form.gender" id="gender" :options="$page.props.genders" placeholder="Select gender"/>
                    <InputError :message="form.errors.gender" />
                </div>
                <div>
                    <DateInput v-model="form.birthdate"></DateInput>
                    <InputError :message="form.errors.birthdate" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Body Information" description="Enter your weight, height and blood type.">
            <div class="space-y-2">
                <div>
                    <TextInput id="height" v-model="form.height" type="text" placeholder="Height in cm" />
                    <InputError :message="form.errors.height" />
                </div>
                <div>
                    <TextInput id="weight" v-model="form.weight" type="text" placeholder="Weight in kg" />
                    <InputError :message="form.errors.weight" />
                </div>
                <div>
                    <SelectInput v-model="form.blood_type" id="blood_type" :options="$page.props.bloodTypes" placeholder="Select blood type" />
                    <InputError :message="form.errors.blood_type" />
                </div>
            </div>
        </FormSection>

        <div class="flex items-center">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="emits('save-user-info')">
                Update
            </PrimaryButton>

            <ActionMessage :on="form.recentlySuccessful">
                Updated.
            </ActionMessage>
        </div>
    </form>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { defineEmits, inject } from 'vue'

import ActionMessage from '@/Components/ActionMessage.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputPhotoUpload from '@/Components/Form/InputPhotoUpload.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'

const form = inject('form')
const emits = defineEmits(['save-user-info'])

const handleUpload = (payload) => {
    form.photo = payload
}

const { route } = window

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['auth'] }),
    })
}
</script>
