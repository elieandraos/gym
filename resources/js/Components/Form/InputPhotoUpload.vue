<template>
    <div>
        <input
            id="photo"
            ref="photoInput"
            type="file"
            class="hidden"
            @input="emits('upload', $event.target.files[0])"
            @change="updatePhotoPreview">

        <!-- Current Profile Photo -->
        <div v-show="! photoPreview" class="mt-2">
            <img :src="photo_url" class="rounded-full h-20 w-20 object-cover" alt="">
        </div>

        <!-- New Profile Photo Preview -->
        <div v-show="photoPreview" class="mt-2">
            <span
                class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                :style="'background-image: url(\'' + photoPreview + '\');'"
            />
        </div>

        <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
            Select A New Photo
        </SecondaryButton>

        <SecondaryButton
            v-if="photo_path"
            type="button"
            class="mt-2"
            @click.prevent="remove">
            Remove Photo
        </SecondaryButton>
    </div>
</template>

<script setup>
import SecondaryButton from '@/Components/Layout/SecondaryButton.vue'
import { ref, defineEmits } from 'vue'

const emits = defineEmits(['upload', 'remove'])

defineProps({
    photo_url: { type: [String, null ], required: false, default: '' },
    photo_path: { type: [String, null ], required: false, default: '' },
})

const photoPreview = ref(null)
const photoInput = ref(null)

const selectNewPhoto = () => {
    photoInput.value.click()
}

const updatePhotoPreview = (event) => {
    const file = event.target.files[0]
    if (!file) return

    const reader = new FileReader()

    reader.onload = (e) => {
        photoPreview.value = e.target.result
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
    clearPhotoFileInput()
    emits('remove')
}
</script>
