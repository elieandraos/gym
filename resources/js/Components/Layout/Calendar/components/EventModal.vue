<template>
    <Modal :show="isOpen" @close="$emit('close')">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    <time :datetime="startTime">{{ shortTime }}</time> - {{ trainer }}
                </h3>
                <div class="mt-4">
                    <div class="space-y-2">
                        <button
                            v-for="(member, index) in members"
                            :key="member"
                            @click="$emit('goToMember', member)"
                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors flex items-center gap-3 cursor-pointer"
                        >
                            <img
                                :src="memberPhotos[index]"
                                :alt="member"
                                class="h-10 w-10 rounded-full object-cover"
                            />
                            <span class="font-medium text-gray-900">{{ member }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <button
                type="button"
                @click="$emit('close')"
                class="inline-flex w-full justify-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 active:bg-gray-900 sm:w-auto cursor-pointer transition ease-in-out duration-150"
            >
                Close
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { computed } from 'vue'

import Modal from '@/Components/Layout/Modal.vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    selectedSlot: {
        type: Object,
        default: null
    }
})

defineEmits(['close', 'goToMember'])

const startTime = computed(() => props.selectedSlot?.start_time)
const shortTime = computed(() => props.selectedSlot?.short_time)
const trainer = computed(() => props.selectedSlot?.trainer)
const members = computed(() => props.selectedSlot?.members || [])
const memberPhotos = computed(() => props.selectedSlot?.member_photos || [])
</script>
