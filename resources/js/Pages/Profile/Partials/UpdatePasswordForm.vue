<template>
    <h3 class="font-medium">Update password</h3>
    <div class="text-sm text-zinc-500">
        Ensure your account is using a strong password to stay secure.
    </div>

    <div class="space-y-4 my-8 lg:w-1/2">
        <div class="col-span-6 sm:col-span-4">
            <InputLabel for="current_password" value="Current Password" />
            <TextInput
                id="current_password"
                v-model="form.current_password"
                type="password"
            />
            <InputError :message="form.errors.current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <InputLabel for="password" value="New Password" />
            <TextInput
                id="password"
                v-model="form.password"
                type="password"
            />
            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <InputLabel for="password_confirmation" value="Confirm Password" />
            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
              />
            <InputError :message="form.errors.password_confirmation" class="mt-2" />
        </div>
    </div>

    <div class="flex gap-4 items-center">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="updatePassword">
            Update password
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful">Updated.</ActionMessage>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

import ActionMessage from '@/Components/ActionMessage.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'

const { route } = window

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
            }

            if (form.errors.current_password) {
                form.reset('current_password')
            }
        },
    })
}
</script>
