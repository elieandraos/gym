<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-zinc-900/20 transition-opacity" @click="close"></div>

            <!-- Modal Container -->
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        :class="maxWidthClass"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full"
                        @click.stop
                    >
                        <!-- Body Content -->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <slot name="footer">
                                <button
                                    type="button"
                                    @click="close"
                                    class="inline-flex w-full justify-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 active:bg-gray-900 sm:w-auto cursor-pointer transition ease-in-out duration-150"
                                >
                                    Close
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    maxWidth: {
        type: String,
        default: 'lg'
    }
})

const emit = defineEmits(['close'])

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth]
})

const close = () => {
    emit('close')
}
</script>
