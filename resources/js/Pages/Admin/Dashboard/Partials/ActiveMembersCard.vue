<template>
    <div class="bg-white rounded-lg p-6">
        <div v-if="loading" class="animate-pulse">
            <div class="flex items-center gap-3 mb-6">
                <div class="size-12 bg-zinc-100 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-zinc-100 rounded w-32"></div>
                    <div class="h-10 bg-zinc-100 rounded w-20"></div>
                </div>
            </div>
            <div class="space-y-3">
                <div v-for="i in 3" :key="i" class="flex items-center gap-3">
                    <div class="size-10 bg-zinc-100 rounded-full"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-zinc-100 rounded w-24"></div>
                        <div class="h-3 bg-zinc-100 rounded w-16"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="flex items-center gap-3 mb-6">
                <div class="flex-shrink-0">
                    <div class="size-12 bg-zinc-100 rounded-lg flex items-center justify-center">
                        <UsersIcon class="size-6 text-zinc-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-zinc-500 mb-1">Active Members</p>
                    <div class="text-4xl font-[600] text-zinc-900 overflow-hidden">
                        <span ref="counterRef">{{ displayValue }}</span>
                    </div>
                </div>
            </div>

            <div v-if="trainers && trainers.length > 0" class="space-y-3 pt-4 border-t border-zinc-100">
                <p class="text-xs text-zinc-500 uppercase tracking-wide font-[500] mb-3">Trainers</p>
                <div v-for="trainer in trainers" :key="trainer.id" class="flex items-center gap-3">
                    <img
                        :src="trainer.profile_photo_url"
                        :alt="trainer.name"
                        class="size-10 rounded-full object-cover border-2 border-zinc-100"
                    />
                    <div class="flex-1">
                        <p class="text-sm font-[500] text-zinc-900">{{ getFirstName(trainer.name) }}</p>
                        <p class="text-xs text-zinc-500">{{ trainer.active_members_count }} {{ trainer.active_members_count === 1 ? 'member' : 'members' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { UsersIcon } from '@heroicons/vue/24/outline'
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    totalMembers: { type: Number, default: 0 },
    trainers: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})

const displayValue = ref(0)
const counterRef = ref(null)

const getFirstName = (fullName) => {
    return fullName.split(' ')[0]
}

const animateCounter = (target) => {
    const duration = 1500 // 1.5 seconds
    const steps = 60
    const increment = target / steps
    const stepDuration = duration / steps
    let current = 0

    const timer = setInterval(() => {
        current += increment
        if (current >= target) {
            displayValue.value = target
            clearInterval(timer)
        } else {
            displayValue.value = Math.floor(current)
        }
    }, stepDuration)
}

watch(() => props.totalMembers, (newValue) => {
    if (!props.loading && newValue > 0) {
        animateCounter(newValue)
    }
}, { immediate: true })

onMounted(() => {
    if (!props.loading && props.totalMembers > 0) {
        animateCounter(props.totalMembers)
    }
})
</script>
