<template>
    <AppLayout title="Dashboard">
        <Container>
            <PageHeader :sticky="true">
                <PageHeaderTitle>Good afternoon, Hanna</PageHeaderTitle>
            </PageHeader>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
