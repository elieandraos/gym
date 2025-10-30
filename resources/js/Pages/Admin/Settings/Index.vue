<template>
    <AppLayout title="Settings">
        <Container :centered="false">
            <PageHeader :sticky="true">
                <div class="flex items-center gap-4">
                    <PageBackButton />
                    <PageHeaderTitle>Settings</PageHeaderTitle>
                </div>
            </PageHeader>

            <SettingsForm :trainers="trainers" :settings="settings" />
        </Container>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import SettingsForm from './Partials/SettingsForm.vue'

defineProps({
    trainers: { type: Array, required: true },
    settings: { type: Object, required: true },
})
</script>
