<template>
    <AppLayout title="Profile">
        <Container>
            <Tabs>
                <Tab title="Profile">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </Tab>
                <Tab title="Password">
                    <UpdatePasswordForm />
                </Tab>
                <Tab title="Security">
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                </Tab>
            </Tabs>
        </Container>
    </AppLayout>
</template>

<script setup>
import Tab from '@/Components/Layout/Tab.vue'
import Tabs from '@/Components/Layout/Tabs.vue'
import { usePage } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'

defineProps({
    sessions: Array,
})

const page = usePage()
</script>
