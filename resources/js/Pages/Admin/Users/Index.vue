<template>
    <AppLayout title="Profile">
        <Container>
            <div>users list</div>
            <pre>{{ users }}</pre>asd
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    users: Array,
})
</script>
