<template>
    <AppLayout title="Dashboard">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="false">
                <PageHeaderTitle>Dashboard</PageHeaderTitle>
            </PageHeader>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-12">
                    <StatsCard
                        :title="`Active Members (avg. age ${stats?.average_age || 0})`"
                        :value="stats?.active_members || 0"
                        :change="stats?.active_members_change"
                        :loading="!stats"
                    />

                    <TrainersBarChart
                        :trainers="trainers || []"
                        :loading="!stats"
                    />

                    <GenderPieChart
                        :male-count="stats?.male_members || 0"
                        :female-count="stats?.female_members || 0"
                        :loading="!stats"
                    />
                </div>

                <!-- Right Column: Bookings Stacked -->
                <div class="space-y-12">
                    <ExpiringSoonCard
                        :bookings="bookings?.expiring || []"
                        :loading="!stats"
                    />

                    <UnpaidCard
                        :unpaid-bookings="bookings?.unpaid || []"
                        :frozen-bookings="bookings?.frozen || []"
                        :loading="!stats"
                        @mark-as-paid="markAsPaid"
                    />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import ExpiringSoonCard from '@/Pages/Admin/Dashboard/Partials/ExpiringSoonCard.vue'
import GenderPieChart from '@/Pages/Admin/Dashboard/Partials/GenderPieChart.vue'
import StatsCard from '@/Pages/Admin/Dashboard/Partials/StatsCard.vue'
import TrainersBarChart from '@/Pages/Admin/Dashboard/Partials/TrainersBarChart.vue'
import UnpaidCard from '@/Pages/Admin/Dashboard/Partials/UnpaidCard.vue'

const { route } = window

const props = defineProps({
    stats: { type: Object, default: null },
    bookings: {
        type: Object,
        default: () => ({
            expiring: [],
            unpaid: [],
            frozen: [],
        }),
    },
    trainers: { type: Array, default: () => [] },
})

const markAsPaid = (bookingId) => {
    router.patch(route('admin.bookings.mark-as-paid', bookingId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove from unpaid list
            const index = props.bookings.unpaid.findIndex(b => b.id === bookingId)
            if (index > -1) {
                props.bookings.unpaid.splice(index, 1)
            }
        }
    })
}
</script>
