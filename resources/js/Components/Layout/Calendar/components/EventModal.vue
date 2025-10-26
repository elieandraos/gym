<template>
    <Teleport to="body">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-50"
        >
            <div class="fixed inset-0 bg-zinc-900/20 transition-opacity" @click="$emit('close')"></div>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                        @click.stop
                    >
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    <time :datetime="selectedSlot?.start_time">{{ selectedSlot?.short_time }}</time> - {{ selectedSlot?.trainer }}
                                </h3>
                                <div class="mt-4">
                                    <div class="space-y-2">
                                        <button
                                            v-for="(member, index) in selectedSlot?.members"
                                            :key="member"
                                            @click="$emit('goToMember', member)"
                                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors flex items-center gap-3 cursor-pointer"
                                        >
                                            <img
                                                :src="selectedSlot?.member_photos[index]"
                                                :alt="member"
                                                class="h-10 w-10 rounded-full object-cover"
                                            />
                                            <span class="font-medium text-gray-900">{{ member }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 active:bg-gray-900 sm:mt-0 sm:w-auto cursor-pointer transition ease-in-out duration-150"
                        >
                            Close
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({
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
</script>