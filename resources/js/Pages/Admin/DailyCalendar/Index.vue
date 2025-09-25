<template>
    <AppLayout title="Daily Calendar">
        <Container>
            <h1 class="text-2xl font-bold mb-6">Daily Calendar Debug View</h1>

            <div class="bg-gray-100 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Props Debug Output:</h2>
                <pre class="text-sm bg-white p-4 rounded border overflow-auto max-h-96">{{ JSON.stringify($props, null, 2) }}</pre>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Container from '@/Components/Layout/Container.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    day: { type: Object, required: true }
})
</script>