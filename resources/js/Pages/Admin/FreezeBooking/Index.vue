<template>
    <AppLayout title="Freeze Training">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="space-y-8">
                <div class="">Are you sure you want to freeze this training? All upcoming sessions will be frozen.</div>

                <div class="flex gap-4">
                    <primary-button @click="freezeBooking">Affirmative!</primary-button>

                    <Link :href="route('admin.members.show', member.id)">
                        <TransparentButton>No</TransparentButton>
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
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    booking: { type: Object, required: true },
})

const { id, member } = props.booking
const { route } = window

const freezeBooking = () => {
    router.patch(route('admin.bookings.freeze.update', id))
}
</script>
