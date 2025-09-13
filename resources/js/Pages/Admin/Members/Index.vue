<template>
    <AppLayout title="Profile">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="true">
                <div class="w-full flex justify-between items-center font-normal">
                    <div class="flex items-center gap-4">
                        <div class="w-64">
                            <text-input
                                v-model="searchQuery"
                                @input="performSearch"
                                placeholder="Search members by name..."
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <switch-input v-model="activeTraining" @change="performSearch" />
                            <label class="text-[#71717b]">Showing members currently training</label>
                        </div>
                    </div>

                    <Link :href="route('admin.members.create')">
                        <primary-button type="button" class="">
                            Add member
                        </primary-button>
                    </Link>
                </div>
            </PageHeader>

            <MembersList :data="data" :headers="headers" :links="meta?.links || []"></MembersList>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MembersList from '@/Pages/Admin/Members/Partials/MembersList.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import SwitchInput from '@/Components/Form/SwitchInput.vue'

const props = defineProps({
    members: Object,
    search: { type: String, default: '' },
    activeTraining: { type: Boolean, default: true },
})

const { route } = window
const { data, meta } = props.members || {}
const headers = ['Name', 'Start date', 'Phone number', 'Age']

const searchQuery = ref(props.search)
const activeTraining = ref(props.activeTraining)

watch(() => props.search, (newSearch) => {
    searchQuery.value = newSearch
})

watch(() => props.activeTraining, (newActiveTraining) => {
    activeTraining.value = newActiveTraining
})

let searchTimeout = null

const performSearch = () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(route('admin.members.index'), {
            search: searchQuery.value,
            activeTraining: activeTraining.value ? 1 : 0
        }, {
            replace: true,
        })
    }, 300)
}
</script>
