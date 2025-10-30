<template>
    <div class="fixed bottom-4 right-4 xl:bottom-4 xl:right-4 max-w-[calc(100vw-2rem)] w-96 z-50" :class="mobileBottomClass">
        <transition name="fade">
            <Banner
                v-if="show && message"
                :type="type"
                :show-close="true"
                @close="closeBanner">
                {{ message }}
            </Banner>
        </transition>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, computed, watchEffect } from 'vue'
import Banner from '@/Components/Banner.vue'

const page = usePage()
const show = ref(true)
const style = ref('success')
const message = ref('')

// Position banner above bottom toolbar on mobile/tablet (64px toolbar + 16px padding + safe area)
const mobileBottomClass = computed(() => {
    return 'bottom-20 xl:bottom-4'
})

const type = computed(() => {
    const styleMap = {
        'success': 'success',
        'danger': 'error'
    }
    return styleMap[style.value] || 'error'
})

const closeBanner = () => {
    show.value = false
}

watchEffect(() => {
    style.value = page.props.jetstream.flash?.bannerStyle || 'danger'
    message.value = page.props.jetstream.flash?.banner || ''
    show.value = true

    setTimeout(() => {
        show.value = false
    }, 2000)
})
</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
