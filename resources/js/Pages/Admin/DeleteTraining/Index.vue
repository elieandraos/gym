<template>
    <AppLayout title="Remove Training">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="space-y-8">
                <div class="space-y-4">
                    <p>Are you sure you want to remove this training for <strong>{{ member.name }}</strong>?</p>
                    <p class="text-zinc-600">This will permanently delete the training and all associated data including:</p>
                    <ul class="list-disc list-inside space-y-2 text-zinc-600 ml-4">
                        <li>Training period: <strong class="text-zinc-900">{{ booking.formatted_start_date }} – {{ booking.formatted_end_date }}</strong></li>
                        <li>{{ booking.nb_sessions }} {{ booking.nb_sessions === 1 ? 'session' : 'sessions' }}</li>
                        <li>{{ booking.completed_sessions }} completed {{ booking.completed_sessions === 1 ? 'session' : 'sessions' }} with workout logs</li>
                        <li>{{ booking.total_circuits }} {{ booking.total_circuits === 1 ? 'circuit' : 'circuits' }}</li>
                        <li>{{ booking.total_exercises }} {{ booking.total_exercises === 1 ? 'exercise' : 'exercises' }}</li>
                    </ul>
                </div>

                <div class="flex gap-4">
                    <DangerButton @click="deleteTraining">Affirmative</DangerButton>

                    <Link :href="route('admin.members.show', member.id)">
                        <TransparentButton>Cancel</TransparentButton>
                    </Link>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/Components/Layout/PageHeader.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import Container from '@/Components/Layout/Container.vue'
import DangerButton from '@/Components/DangerButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    booking: { type: Object, required: true },
    member: { type: Object, required: true },
})

const { route } = window

const deleteTraining = () => {
    router.delete(route('admin.bookings.destroy', props.booking.id))
}
</script>
