<template>
    <AppLayout title="Profile">
        <Container>
            <Tabs>
                <Tab title="Profile">
                    <UpdateProfileInformationForm @save-user-info="saveUser" />
                </Tab>
                <Tab title="Contact">
                    <UpdateContactInformation @save-user-info="saveUser"/>
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
import { usePage, useForm } from '@inertiajs/vue3'
import { provide } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import Tab from '@/Components/Layout/Tab.vue'
import Tabs from '@/Components/Layout/Tabs.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue'
import UpdateContactInformation from '@/Pages/Profile/Partials/UpdateContactInformation.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'

defineProps({
    sessions: Array,
})

const page = usePage()

const { route } = window

const form = useForm({
    _method: 'PUT',
    name: page.props.auth.user.name,
    gender: page.props.auth.user.gender,
    birthdate: page.props.auth.user.birthdate,
    weight: page.props.auth.user.weight,
    height: page.props.auth.user.height,
    blood_type: page.props.auth.user.blood_type,
    phone_number: page.props.auth.user.phone_number,
    email: page.props.auth.user.email,
    instagram_handle: page.props.auth.user.instagram_handle,
    address: page.props.auth.user.address,
    emergency_contact: page.props.auth.user.emergency_contact,
    photo: null,
})

provide('form', form)

const saveUser = () => {
    // if (photoInput.value) {
    // form.photo = photoInput.value.files[0]

    form.put(route('user-profile-information.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.recentlySuccessful = true
            setTimeout(() => {
                form.recentlySuccessful = false
            }, 2000)
        },
    })
}
</script>
