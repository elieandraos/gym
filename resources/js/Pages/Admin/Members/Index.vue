<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="true">
                <div class="flex w-full justify-between items-center gap-2 font-normal">
                    <members-search
                        :search="searchQuery"
                        @update:search="handleSearchChange"
                    />

                    <div class="flex items-center gap-2">
                        <members-filters
                            :training-status="trainingStatus"
                            @update:training-status="handleFilterChange"
                        />

                        <Link :href="route('admin.members.create')">
                            <primary-button type="button">
                                <PlusIcon class="size-5" />
                            </primary-button>
                        </Link>
                    </div>
                </div>
            </PageHeader>

            <MembersList :data="data" :headers="headers" :links="meta?.links || []"></MembersList>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/solid'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import MembersFilters from '@/Pages/Admin/Members/Partials/MembersFilters.vue'
import MembersList from '@/Pages/Admin/Members/Partials/MembersList.vue'
import MembersSearch from '@/Pages/Admin/Members/Partials/MembersSearch.vue'

const props = defineProps({
    members: Object,
    search: { type: String, default: '' },
    trainingStatus: { type: String, default: 'all' },
})

const { route } = window
const { data, meta } = props.members || {}
const headers = ['Name', 'Start date', 'Phone number', 'Age']

const searchQuery = ref(props.search)
const trainingStatus = ref(props.trainingStatus)

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

watch(() => props.trainingStatus, (newTrainingStatus) => {
    trainingStatus.value = newTrainingStatus
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(route('admin.members.index'), {
            search: searchQuery.value,
            trainingStatus: trainingStatus.value
        }, {
            replace: true,
        })
    }, 300)
}

const handleSearchChange = (value) => {
    searchQuery.value = value
    performSearch()
}

const handleFilterChange = (value) => {
    trainingStatus.value = value
    performSearch()
}
</script>
