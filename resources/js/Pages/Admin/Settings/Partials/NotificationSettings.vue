<template>
    <form @submit.prevent="submit">
        <FormSection
            title="Notification Settings"
            description="Configure email notifications for gym owners and administrators."
        >
            <div class="space-y-4">
                <!-- Send Email to Owners Toggle -->
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-700">Send email when new member is created</span>
                    <SwitchInput v-model="form.notifications.new_member_email_to_owners" />
                </div>
                <InputError :message="form.errors['notifications.new_member_email_to_owners']" />

                <!-- Owner Emails Input (shown when toggle is on) -->
                <div v-if="form.notifications.new_member_email_to_owners">
                    <TextInput
                        v-model="form.notifications.owner_emails"
                        type="text"
                        placeholder="owner@gym.com, admin@gym.com"
                    />
                    <InputError :message="form.errors['notifications.owner_emails']" />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-2">
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                    >
                        Save Settings
                    </PrimaryButton>
                </div>
            </div>
        </FormSection>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SwitchInput from '@/Components/Form/SwitchInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'

const { route } = window

const props = defineProps({
    settings: { type: Object, required: true },
})

// Form setup
const form = useForm({
    notifications: {
        new_member_email_to_owners: props.settings.notifications?.new_member_email_to_owners ?? true,
        owner_emails: props.settings.notifications?.owner_emails ?? '',
    },
})

const submit = () => {
    form.patch(route('admin.settings.update'), {
        preserveScroll: true,
    })
}
</script>
