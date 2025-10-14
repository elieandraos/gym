<template>
    <AppLayout title="Dashboard">
        <Container>
            <PageHeader :sticky="true" :bordered="false" :bottom-gap="false">
                <div class="flex flex-col gap-2">
                    <div class="flex gap-8 items-start">
                        <div>
                            <PageHeaderTitle>Ready to crush today?</PageHeaderTitle>
                            <span
                                v-if="!showQuote"
                                @click="showRandomQuote"
                                class="text-xs text-sky-500 hover:text-sky-700 cursor-pointer">
                                Nah, motivate me! 😬
                            </span>
                        </div>
                    </div>
                    <div v-if="showQuote" class="w-full flex items-center gap-2 text-sm italic text-zinc-600">
                        <span>"{{ currentQuote }}"</span>
                        <ArrowPathIcon
                            @click="showRandomQuote"
                            class="w-4 h-4 text-sky-500 hover:text-sky-700 cursor-pointer flex-shrink-0" />
                    </div>
                </div>
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
import { ArrowPathIcon } from '@heroicons/vue/24/outline'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

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

const motivationalQuotes = [
    'Your energy sets the tone. Show up with purpose, leave with impact.',
    'Great trainers don\'t just count reps, they make every rep count.',
    'You\'re not just building bodies, you\'re building confidence and changing lives.',
    'The best program is the one your client will actually follow. Meet them where they are.',
    'Your passion is contagious. Use it to ignite their fire.',
    'Celebrate the small wins - they\'re building blocks to big transformations.',
    'Listen more than you prescribe. Every client has a story worth understanding.',
    'Consistency beats intensity. Be the example your clients need to see.',
    'Your client\'s breakthrough starts with your belief in them.',
    'Knowledge gets clients in the door. Your presence keeps them coming back.',
    'Progress isn\'t linear. Be the steady hand when their journey gets rocky.',
    'You\'re a coach, mentor, and sometimes a therapist. Wear all hats with pride.',
    'Every session is an opportunity to inspire. Make it count.',
    'Form over ego. Teach quality movement, not just heavier weights.',
    'Your feedback today shapes their habits tomorrow. Choose your words wisely.',
    'Some days you\'re the motivator. Other days you\'re the accountability partner. Both matter.',
    'The best trainers stay students. Never stop learning, never stop growing.',
    'You can\'t pour from an empty cup. Take care of yourself to take care of others.',
    'See potential where others see limitations. That\'s your superpower.',
    'Building trust takes time. Be patient, be present, be consistent.',
]

const showQuote = ref(false)
const currentQuote = ref('')

const showRandomQuote = () => {
    const randomIndex = Math.floor(Math.random() * motivationalQuotes.length)
    currentQuote.value = motivationalQuotes[randomIndex]
    showQuote.value = true
}

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
