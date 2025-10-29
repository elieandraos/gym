<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="true">
                <div class="flex w-full justify-between items-center gap-2 font-normal">
                    <workouts-search
                        :search="searchQuery"
                        @update:search="handleSearchChange"
                    />

                    <div class="flex items-center gap-2">
                        <workouts-filters
                            :selected-categories="selectedCategories"
                            :categories="props.categories"
                            @update:categories="handleCategoriesChange"
                        />

                        <Link :href="route('admin.workouts.create')">
                            <primary-button type="button">
                                <PlusIcon class="size-5" />
                            </primary-button>
                        </Link>
                    </div>
                </div>
            </PageHeader>

            <WorkoutsList :data="data" :headers="headers" :links="meta?.links || []"></WorkoutsList>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/solid'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import WorkoutsFilters from '@/Pages/Admin/Workouts/Partials/WorkoutsFilters.vue'
import WorkoutsList from '@/Pages/Admin/Workouts/Partials/WorkoutsList.vue'
import WorkoutsSearch from '@/Pages/Admin/Workouts/Partials/WorkoutsSearch.vue'

const props = defineProps({
    workouts: Object,
    search: { type: String, default: '' },
    categories: { type: Array, default: () => [] },
    selectedCategories: { type: Array, default: () => [] },
})

const { route } = window
const { data, meta } = props.workouts || {}
const headers = ['Name', 'Category', '']

const searchQuery = ref(props.search)
const selectedCategories = ref(props.selectedCategories)

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

watch(() => props.selectedCategories, (newCategories) => {
    selectedCategories.value = newCategories
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(route('admin.workouts.index'), {
            search: searchQuery.value,
            categories: selectedCategories.value
        }, {
            replace: true,
        })
    }, 300)
}

const handleSearchChange = (value) => {
    searchQuery.value = value
    performSearch()
}

const handleCategoriesChange = (value) => {
    selectedCategories.value = value
    performSearch()
}
</script>
