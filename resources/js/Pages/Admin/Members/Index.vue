<template>
    <AppLayout title="Profile">
        <Container>
            <page-title :sticky="true">
                <div class="pb-4 w-full flex justify-between items-center">
                    <div>Members list</div>
                    <Link :href="route('admin.members.create')">
                        <primary-button type="button" class="">
                            Add member
                        </primary-button>
                    </Link>
                </div>
            </page-title>

            <members-list :data="data" :headers="headers" :links="meta.links"></members-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MembersList from '@/Pages/Admin/Members/Partials/MembersList.vue'

const props = defineProps({
    members: Object,
})

const { route } = window
const { data, meta } = props.members
const headers = ['Name', 'Start date', 'Phone number', 'Age']
</script>
