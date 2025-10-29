<template>
    <AppLayout title="Member Created">
        <Container>
            <div class="flex flex-col items-center justify-center min-h-[60vh] py-12">
                <div class="text-center space-y-6 max-w-2xl">
                    <!-- Celebration Message -->
                    <div class="space-y-2">
                        <h1 class="text-3xl font-[600] text-gray-900">
                            The squad just got bigger!
                        </h1>
                        <p class="text-lg text-zinc-600">
                            {{ member.name }} is now part of the lift station family
                        </p>
                    </div>

                    <!-- Member Info Card -->
                    <div class="bg-zinc-50 rounded-lg p-6 mx-auto max-w-md">
                        <div class="flex items-center gap-4">
                            <img
                                v-if="member.profile_photo_url"
                                :src="member.profile_photo_url"
                                :alt="member.name"
                                class="w-16 h-16 rounded-full object-cover"
                            />
                            <div v-else class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <UserIcon class="w-8 h-8 text-gray-400" />
                            </div>
                            <div class="text-left flex-1">
                                <p class="font-[600] text-gray-900">{{ member.name }}</p>
                                <p class="text-sm text-zinc-600">{{ member.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        <Link :href="route('admin.bookings.create', { member_id: member.id })">
                            <PrimaryButton type="button" class="w-full sm:w-auto px-8 py-3 text-base">
                                Start Training
                            </PrimaryButton>
                        </Link>
                        <Link :href="route('admin.members.show', member.id)">
                            <TransparentButton type="button" class="w-full sm:w-auto px-8 py-3 text-base">
                                View Profile
                            </TransparentButton>
                        </Link>
                    </div>

                    <!-- Quick Link Back -->
                    <div class="pt-4">
                        <a
                            @click.prevent="celebrate"
                            href="#"
                            class="text-sky-500 hover:text-sky-700 font-[400] cursor-pointer"
                        >
                            Oh wait! Let's celebrate 🎉
                        </a>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { UserIcon } from '@heroicons/vue/24/outline'
import confetti from 'canvas-confetti'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

const { route } = window

defineProps({
    member: { type: Object, required: true },
})

const celebrate = () => {
    const count = 200
    const defaults = {
        origin: { y: 0.7 }
    }

    function fire(particleRatio, opts) {
        confetti({
            ...defaults,
            ...opts,
            particleCount: Math.floor(count * particleRatio)
        })
    }

    fire(0.25, {
        spread: 26,
        startVelocity: 55,
    })
    fire(0.2, {
        spread: 60,
    })
    fire(0.35, {
        spread: 100,
        decay: 0.91,
        scalar: 0.8
    })
    fire(0.1, {
        spread: 120,
        startVelocity: 25,
        decay: 0.92,
        scalar: 1.2
    })
    fire(0.1, {
        spread: 120,
        startVelocity: 45,
    })
}
</script>
