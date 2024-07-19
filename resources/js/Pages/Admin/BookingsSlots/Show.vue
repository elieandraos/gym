<template>
    <AppLayout title="Booking">
        <Container>
            <page-back-button>Back</page-back-button>
            <pre>{{ bookingSlot }}</pre>
        </Container>
    </AppLayout>
</template>

<script setup>


import Container from '@/Components/Layout/Container.vue'
import PageBackButton from '@/Components/Layout/PageBackButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    bookingSlot: { type: Object, required: true }
})
</script>
