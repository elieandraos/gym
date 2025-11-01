<template>
    <FormSection title="Update Password" description="Ensure your account is using a strong password to stay secure." :separator="false">
        <div class="space-y-2">
            <div>
                <TextInput
                    id="current_password"
                    v-model="form.current_password"
                    type="password"
                    placeholder="Current Password"/>
                <InputError :message="form.errors.current_password" />
            </div>

            <div>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="New Password"/>
                <InputError :message="form.errors.password" />
            </div>

            <div>
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Confirm Password"/>
                <InputError :message="form.errors.password_confirmation" />
            </div>
        </div>
    </FormSection>

    <div class="flex gap-4 items-center justify-end">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="updatePassword">
            Update password
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful">Updated.</ActionMessage>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

import ActionMessage from '@/Components/ActionMessage.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
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
