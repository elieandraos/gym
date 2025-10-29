<template>
    <AppLayout title="Profile">
        <Container>
            <page-header :sticky="true" :bordered="false" :bottom-gap="true">
                <div class="flex w-full justify-between items-center gap-2 font-normal">
                    <trainers-search
                        :search="searchQuery"
                        @update:search="handleSearchChange"
                    />

                    <Link :href="route('admin.trainers.create')">
                        <primary-button type="button">
                            <PlusIcon class="size-5" />
                        </primary-button>
                    </Link>
                </div>
            </page-header>

            <trainers-list :data="data" :headers="headers" :links="meta.links"></trainers-list>
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
import TrainersList from '@/Pages/Admin/Trainers/Partials/TrainersList.vue'
import TrainersSearch from '@/Pages/Admin/Trainers/Partials/TrainersSearch.vue'

const props = defineProps({
    trainers: Object,
    search: { type: String, default: '' },
})

const { route } = window
const { data, meta } = props.trainers
const headers = ['Name', 'Start date', 'Phone number', 'Age']

const searchQuery = ref(props.search)

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(route('admin.trainers.index'), {
            search: searchQuery.value
        }, {
            replace: true,
        })
    }, 300)
}

const handleSearchChange = (value) => {
    searchQuery.value = value
    performSearch()
}
</script>
