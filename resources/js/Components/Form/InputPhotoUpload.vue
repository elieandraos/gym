<template>
    <div>
        <input
            id="photo"
            ref="photoInput"
            type="file"
            class="hidden"
            @input="emits('upload', $event.target.files[0])"
            @change="updatePhotoPreview">

        <div class="flex gap-2 items-center">
            <div>
                <!-- Current Profile Photo -->
                <div v-show="photo_url && ! photoPreview && ! photoRemoved">
                    <img :src="photo_url" class="rounded-full size-14 object-cover" alt="">
                </div>
                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview">
                    <span
                        class="block rounded-full size-14 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>
                <!-- Photo Removed State - Show Initials -->
                <div v-show="photoRemoved && !photoPreview" class="rounded-full size-14 bg-gray-100 flex items-center justify-center">
                    <span class="text-gray-500 text-lg font-semibold">{{ getInitials() }}</span>
                </div>
            </div>
            <div>
                <TransparentButton type="button" @click.prevent="selectNewPhoto">
                    Upload photo
                </TransparentButton>

                <DangerButton
                    v-if="(photo_path && !photoRemoved) || photoPreview"
                    type="button"
                    @click.prevent="remove">
                    Remove Photo
                </DangerButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import DangerButton from '@/Components/DangerButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

import { ref, defineEmits } from 'vue'

const emits = defineEmits(['upload', 'remove'])

const props = defineProps({
    photo_url: { type: [String, null], required: false, default: '' },
    photo_path: { type: [String, null], required: false, default: '' },
    name: { type: String, required: false, default: '' },
})

const photoPreview = ref(null)
const photoInput = ref(null)
const photoRemoved = ref(false)

const getInitials = () => {
    if (!props.name) return '?'
    const words = props.name.trim().split(' ')
    if (words.length === 1) return words[0].charAt(0).toUpperCase()
    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase()
}

const selectNewPhoto = () => {
    photoInput.value.click()
}

const updatePhotoPreview = (event) => {
    const file = event.target.files[0]
    if (!file) return

    const reader = new FileReader()

    reader.onload = (e) => {
        photoPreview.value = e.target.result
        photoRemoved.value = false
    }

    reader.readAsDataURL(file)
}

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null
    }
}

const remove = () => {
    photoPreview.value = null
    photoRemoved.value = true
    clearPhotoFileInput()
    emits('remove')
}
</script>
