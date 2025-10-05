<template>
    <AppLayout title="Dashboard">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="true">
                <PageHeaderTitle>Dashboard</PageHeaderTitle>
            </PageHeader>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Active Members Widget -->
                <div class="md:col-span-2 lg:col-span-1">
                    <ActiveMembersCard
                        :total-members="stats?.active_members || 0"
                        :trainers="trainers || []"
                        :loading="!stats"
                    />
                </div>

                <!-- Unpaid Bookings -->
                <StatsCard
                    title="Unpaid Bookings"
                    :value="stats?.unpaid_bookings || 0"
                    :loading="!stats"
                >
                    <template #icon>
                        <div class="size-12 bg-red-50 rounded-lg flex items-center justify-center">
                            <CurrencyDollarIcon class="size-6 text-red-400" />
                        </div>
                    </template>
                </StatsCard>

                <!-- Frozen Bookings -->
                <StatsCard
                    title="Frozen Bookings"
                    :value="stats?.frozen_bookings || 0"
                    :loading="!stats"
                >
                    <template #icon>
                        <div class="size-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <StopIcon class="size-6 text-blue-400" />
                        </div>
                    </template>
                </StatsCard>

                <!-- Expiring Bookings -->
                <StatsCard
                    title="About to Expire"
                    :value="stats?.expiring_bookings || 0"
                    subtitle="2 sessions remaining"
                    :loading="!stats"
                >
                    <template #icon>
                        <div class="size-12 bg-amber-50 rounded-lg flex items-center justify-center">
                            <ClockIcon class="size-6 text-amber-400" />
                        </div>
                    </template>
                </StatsCard>

                <!-- Male Members -->
                <StatsCard
                    title="Male Members"
                    :value="stats?.male_members || 0"
                    :loading="!stats"
                >
                    <template #icon>
                        <div class="size-12 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <UserIcon class="size-6 text-indigo-400" />
                        </div>
                    </template>
                </StatsCard>

                <!-- Female Members -->
                <StatsCard
                    title="Female Members"
                    :value="stats?.female_members || 0"
                    :loading="!stats"
                >
                    <template #icon>
                        <div class="size-12 bg-pink-50 rounded-lg flex items-center justify-center">
                            <UserIcon class="size-6 text-pink-400" />
                        </div>
                    </template>
                </StatsCard>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { ClockIcon, CurrencyDollarIcon, StopIcon, UserIcon } from '@heroicons/vue/24/outline'

import Container from '@/Components/Layout/Container.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PageHeaderTitle from '@/Components/Layout/PageHeaderTitle.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import ActiveMembersCard from '@/Pages/Admin/Dashboard/Partials/ActiveMembersCard.vue'
import StatsCard from '@/Pages/Admin/Dashboard/Partials/StatsCard.vue'

defineProps({
    stats: { type: Object, default: null },
    trainers: { type: Array, default: () => [] },
})
</script>
