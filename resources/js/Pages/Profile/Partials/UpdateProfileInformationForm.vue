<template>
    <h3 class="font-medium">Profile information</h3>
    <div class="text-sm text-zinc-500">
        Your profile details helps us provide a more personalized experience.
    </div>

    <form @submit.prevent="emits('save-user-info')">
        <div class="my-8">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-8 my-4">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text"/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="gender" value="Gender" />
                    <SelectInput v-model="form.gender" id="gender" :options="$page.props.genders" />
                    <InputError :message="form.errors.gender" />
                </div>
                <div>
                    <InputLabel for="birthdate" value="Birthdate" />
                    <DateInput v-model="form.birthdate"></DateInput>
                    <InputError :message="form.errors.birthdate" />
                </div>
                <div>
                    <InputLabel for="height" value="Height" />
                    <TextInput id="height" v-model="form.height" type="text" />
                    <InputError :message="form.errors.height" />
                </div>
                <div>
                    <InputLabel for="weight" value="Weight" />
                    <TextInput id="weight" v-model="form.weight" type="text" />
                    <InputError :message="form.errors.weight" />
                </div>
                <div>
                    <InputLabel for="blood_type" value="Blood type" />
                    <SelectInput v-model="form.blood_type" id="blood_type" :options="$page.props.bloodTypes" />
                    <InputError :message="form.errors.blood_type" />
                </div>
            </div>
        </div>

        <div class="space-y-4 my-8 lg:w-1/2">
            <InputLabel for="photo" value="Photo" />
            <InputPhotoUpload
                @upload="handleUpload"
                @remove="deletePhoto"
                :photo_url="$page.props.auth.user.profile_photo_url"
                :photo_path="$page.props.auth.user.profile_photo_path">
            </InputPhotoUpload>
            <InputError :message="form.errors.photo" />
        </div>

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
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
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
