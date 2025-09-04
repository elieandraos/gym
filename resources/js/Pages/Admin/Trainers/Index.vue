<template>
    <AppLayout title="Profile">
        <Container>
            <page-title :sticky="true">
                <div class="pb-8 w-full flex justify-between items-center font-normal">
                    <div class="flex items-center gap-4">
                        <div class="w-96">
                            <text-input
                                v-model="searchQuery"
                                @input="performSearch"
                                placeholder="Search trainers by name..."
                            />
                        </div>
                    </div>

                    <Link :href="route('admin.trainers.create')">
                        <primary-button type="button" class="">
                            Add trainer
                        </primary-button>
                    </Link>
                </div>
            </page-title>

            <trainers-list :data="data" :headers="headers" :links="meta.links"></trainers-list>
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
import TrainersList from '@/Pages/Admin/Trainers/Partials/TrainersList.vue'
import TextInput from '@/Components/Form/TextInput.vue'

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
</script>
