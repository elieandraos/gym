<template>
    <FormSection title="Contact Information" description="Enter your email, phone and address information.">
        <div class="space-y-2">
            <div>
                <TextInput id="email" v-model="form.email" type="text" placeholder="Email"/>
                <InputError :message="form.errors.email" />
            </div>
            <div>
                <TextInput id="phone_number" v-model="form.phone_number" type="text" placeholder="Phone number"/>
                <InputError :message="form.errors.phone_number" />
            </div>
            <div>
                <TextInput id="address" v-model="form.address" type="text" placeholder="Location"/>
                <InputError :message="form.errors.address" />
            </div>
        </div>
    </FormSection>

    <FormSection title="Emergency Contact" description="Enter your emergency contact information.">
        <TextInput id="emergency_contact" v-model="form.emergency_contact" type="text" placeholder="Emergency Contact"/>
        <InputError :message="form.errors.emergency_contact" />
    </FormSection>

    <FormSection title="Social" description="Enter your instagram handle." :separator="false">
        <TextInput id="instagram_handle" v-model="form.instagram_handle" type="text" placeholder="Instagram handle"/>
        <InputError :message="form.errors.instagram_handle" />
    </FormSection>

    <FormButtons>
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="saveUserInfo">
            Update
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful">
            Updated.
        </ActionMessage>
    </FormButtons>
</template>

<script setup>
import { inject, defineEmits } from 'vue'

import ActionMessage from '@/Components/ActionMessage.vue'
import FormButtons from '@/Components/Form/FormButtons.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'

const form = inject('form')

const emits = defineEmits(['save-user-info'])

const saveUserInfo = () => {
    emits('save-user-info')
}
</script>
