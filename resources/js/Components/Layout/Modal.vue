<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-zinc-900/20 transition-opacity" @click="close"></div>

            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center px-12 py-12">
                <div
                    :class="maxWidthClass"
                    class="relative flex flex-col rounded-lg bg-white text-left shadow-xl transition-all w-full max-h-[calc(100vh-6rem)] overflow-hidden"
                    @click.stop
                >
                        <!-- Body Content (Scrollable) -->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 overflow-y-auto flex-1">
                            <slot />
                        </div>

                        <!-- Footer (Always Visible) -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 flex-shrink-0 rounded-b-lg">
                            <slot name="footer">
                                <button
                                    type="button"
                                    @click="close"
                                    class="inline-flex w-full justify-center rounded-md bg-zinc-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-800 active:bg-zinc-950 sm:w-auto cursor-pointer transition ease-in-out duration-150"
                                >
                                    Close
                                </button>
                            </slot>
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
        '4xl': 'sm:max-w-4xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl',
        'full': 'sm:max-w-full',
    }[props.maxWidth]
})

const close = () => {
    emit('close')
}
</script>
