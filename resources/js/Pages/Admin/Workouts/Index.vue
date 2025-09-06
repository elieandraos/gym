<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="false">
                <div class="w-full flex justify-between items-center font-normal">
                    <div class="flex items-center gap-4">
                        <div class="w-64">
                            <text-input
                                v-model="searchQuery"
                                @input="performSearch"
                                placeholder="Search workouts..."
                            />
                        </div>
                        <div class="w-48">
                            <select-input
                                v-model="categoryFilter"
                                :options="categoryOptions"
                                placeholder="All categories"
                                @change="performSearch"
                            />
                        </div>
                    </div>

                    <Link :href="route('admin.workouts.create')">
                        <primary-button type="button" class="">
                            Add workout
                        </primary-button>
                    </Link>
                </div>
            </PageHeader>

            <workouts-list :data="data" :headers="headers" :links="meta?.links || []"></workouts-list>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import WorkoutsList from '@/Pages/Admin/Workouts/Partials/WorkoutsList.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'

const props = defineProps({
    workouts: Object,
    search: { type: String, default: '' },
    category: { type: String, default: '' },
    categories: { type: Array, default: () => [] },
})

const { route } = window
const { data, meta } = props.workouts || {}
const headers = ['Name', 'Category', '']

const searchQuery = ref(props.search)
const categoryFilter = ref(props.category)

const categoryOptions = computed(() => {
    return [
        { name: 'All categories', value: '' },
        ...props.categories.map(category => ({ name: category, value: category }))
    ]
})

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

watch(() => props.category, (newCategory) => {
    categoryFilter.value = newCategory
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(route('admin.workouts.index'), {
            search: searchQuery.value,
            category: categoryFilter.value
        }, {
            replace: true,
        })
    }, 300)
}
</script>
