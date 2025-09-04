<template>
    <AppLayout title="Profile">
        <Container>
            <page-title :sticky="true">
                <div class="pb-4 w-full flex justify-between items-center">
                    <div class="w-96">
                        <text-input
                            v-model="searchQuery"
                            @input="performSearch"
                            placeholder="Search members by name..."
                        />
                    </div>

                    <Link :href="route('admin.members.create')">
                        <primary-button type="button" class="">
                            Add member
                        </primary-button>
                    </Link>
                </div>
            </page-title>

            <members-list :data="data" :headers="headers" :links="meta?.links || []"></members-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageTitle from '@/Components/Layout/PageTitle.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MembersList from '@/Pages/Admin/Members/Partials/MembersList.vue'
import TextInput from '@/Components/Form/TextInput.vue'

const props = defineProps({
    members: Object,
    search: { type: String, default: '' },
})

const { route } = window
const { data, meta } = props.members || {}
const headers = ['Name', 'Start date', 'Phone number', 'Age']

const searchQuery = ref(props.search)

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(route('admin.members.index'), { search: searchQuery.value }, {
            replace: true,
        })
    }, 300)
}
</script>
