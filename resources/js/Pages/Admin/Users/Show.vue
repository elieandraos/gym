<template>
    <AppLayout title="Profile">
        <Container>
            <h1 class="text-zinc-900 font-medium text-lg lg:text-xl capitalize pt-12 mb-4 md:pt-5 md:mb-8">{{ name }}</h1>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    user: { type: Object, required: true }
})

const { name } = props.user
</script>
