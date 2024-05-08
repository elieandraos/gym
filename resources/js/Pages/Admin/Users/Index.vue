<template>
    <AppLayout title="Profile">
        <Container>
            <div>users list</div>
            <pre>{{ users }}</pre>
        </Container>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Container.vue'

defineProps({
    users: Array
})
</script>
