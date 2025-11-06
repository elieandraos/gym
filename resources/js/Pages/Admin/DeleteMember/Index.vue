<template>
    <AppLayout title="Delete Member">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <div class="space-y-8">
                <div class="space-y-4">
                    <p class="">Are you sure you want to delete <strong>{{ member.name }}</strong>?</p>
                    <p class="text-zinc-600">This will permanently delete the member and all associated data including:</p>
                    <ul class="list-disc list-inside space-y-2 text-zinc-600 ml-4">
                        <li>All bookings and training sessions</li>
                        <li>All workout logs and exercise data</li>
                        <li>All body composition records</li>
                        <li>All uploaded photos and files</li>
                    </ul>
                </div>

                <div class="flex gap-4">
                    <DangerButton @click="deleteMember">Affirmative</DangerButton>

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
    member: { type: Object, required: true },
})

const { route } = window

const deleteMember = () => {
    router.delete(route('admin.members.destroy', props.member.id))
}
</script>
