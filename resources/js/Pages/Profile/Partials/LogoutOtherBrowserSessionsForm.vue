<template>
    <h3 class="font-medium mt-4">Browser sessions</h3>
    <div class="text-sm text-zinc-500">
        Manage and log out your active sessions on other browsers and devices.
    </div>

    <!-- Other Browser Sessions -->
    <div v-if="sessions.length > 0" class="space-y-6 my-8">
        <div v-for="({ agent, ip_address, is_current_device, last_active }, i) in sessions" :key="i" class="flex items-center">
            <div>
                <svg v-if="agent.is_desktop" class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                </svg>

                <svg v-else class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
            </div>

            <div class="ms-3">
                <div class="text-sm text-gray-600">
                    {{ agent.platform ? agent.platform : 'Unknown' }} - {{ agent.browser ? agent.browser : 'Unknown' }}
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        {{ ip_address }},

                        <span v-if="is_current_device" class="text-green-500 font-semibold">This device</span>
                        <span v-else>Last active {{ last_active }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center mt-5">
        <PrimaryButton @click="confirmLogout">
            Log Out Other Browser Sessions
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful" class="ms-3">
            Done.
        </ActionMessage>
    </div>

<!--     Log Out Other Devices Confirmation Modal-->
    <DialogModal :show="confirmingLogout" @close="closeModal">
        <template #title>
            Log Out Other Browser Sessions
        </template>

        <template #content>
            Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.

            <div class="mt-4">
                <TextInput
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                    autocomplete="current-password"
                    @keyup.enter="logoutOtherBrowserSessions"/>

                <InputError :message="form.errors.password" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>

            <PrimaryButton
                class="ms-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="logoutOtherBrowserSessions">
                Log Out Other Browser Sessions
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import ActionMessage from '@/Components/ActionMessage.vue'
import DialogModal from '@/Components/DialogModal.vue'
import InputError from '@/Components/Form/InputError.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'

defineProps({
    sessions: Array,
})

const { route } = window

const confirmingLogout = ref(false)
const passwordInput = ref(null)

const form = useForm({
    password: '',
})

const confirmLogout = () => {
    confirmingLogout.value = true
}

const closeModal = () => {
    confirmingLogout.value = false

    form.reset()
}

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    })
}
</script>
