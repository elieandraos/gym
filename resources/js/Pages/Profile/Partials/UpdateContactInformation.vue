<template>
    <h3 class="font-medium">Contact information</h3>
    <div class="text-sm text-zinc-500">
        Your contact details help us reach you promptly for any necessary communications.
    </div>

    <div class="my-8">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-8 my-4">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="text"/>
                <InputError :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="phone_number" value="Phone number" />
                <TextInput id="phone_number" v-model="form.phone_number" type="text"/>
                <InputError :message="form.errors.phone_number" />
            </div>
            <div>
                <InputLabel for="instagram_handle" value="Instagram handle" />
                <TextInput id="instagram_handle" v-model="form.instagram_handle" type="text"/>
                <InputError :message="form.errors.instagram_handle" />
            </div>
            <div>
                <InputLabel for="address" value="Address" />
                <TextInput id="address" v-model="form.address" type="text"/>
                <InputError :message="form.errors.address" />
            </div>
            <div>
                <InputLabel for="emergency_contact" value="Emergency Contact" />
                <TextInput id="emergency_contact" v-model="form.emergency_contact" type="text"/>
                <InputError :message="form.errors.emergency_contact" />
            </div>
        </div>
    </div>

    <div class="flex items-center my-8">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="saveUserInfo">
            Update
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful">
            Updated.
        </ActionMessage>
    </div>
</template>

<script setup>
import { inject, defineEmits } from 'vue'

import ActionMessage from '@/Components/ActionMessage.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'

const form = inject('form')

const emits = defineEmits(['save-user-info'])

const saveUserInfo = () => {
    emits('save-user-info')
}
</script>
