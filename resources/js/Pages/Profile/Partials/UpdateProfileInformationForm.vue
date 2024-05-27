<template>
    <h3 class="font-medium">Profile information</h3>
    <div class="text-sm text-zinc-500">
        Your profile details helps us provide a more personalized experience.
    </div>

    <div class="my-8">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-8 my-4">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" autofocus/>
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="gender" value="Gender" />
                <SelectInput v-model="form.gender" id="gender" :options="$page.props.genders" />
                <InputError :message="form.errors.gender" />
            </div>
            <div>
                <InputLabel for="birthdate" value="Birthdate" />
                <DatepickerInput v-model="form.birthdate"></DatepickerInput>
                <InputError :message="form.errors.birthdate" />
            </div>
            <div>
                <InputLabel for="height" value="Height" />
                <TextInput id="height" v-model="form.height" type="text" />
                <InputError :message="form.errors.height" />
            </div>
            <div>
                <InputLabel for="weight" value="Weight" />
                <TextInput id="weight" v-model="form.weight" type="text" />
                <InputError :message="form.errors.weight" />
            </div>
            <div>
                <InputLabel for="blood_type" value="Blood type" />
                <SelectInput v-model="form.blood_type" id="blood_type" :options="$page.props.bloodTypes" />
                <InputError :message="form.errors.blood_type" />
            </div>
        </div>
    </div>


<!--    <div class="space-y-4 my-8 lg:w-1/2">-->
<!--        &lt;!&ndash; Profile Photo &ndash;&gt;-->
<!--        <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">-->
<!--            &lt;!&ndash; Profile Photo File Input &ndash;&gt;-->
<!--            <input-->
<!--                id="photo"-->
<!--                ref="photoInput"-->
<!--                type="file"-->
<!--                class="hidden"-->
<!--                @change="updatePhotoPreview">-->

<!--            <InputLabel for="photo" value="Photo" />-->

<!--            &lt;!&ndash; Current Profile Photo &ndash;&gt;-->
<!--            <div v-show="! photoPreview" class="mt-2">-->
<!--                <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">-->
<!--            </div>-->

<!--            &lt;!&ndash; New Profile Photo Preview &ndash;&gt;-->
<!--            <div v-show="photoPreview" class="mt-2">-->
<!--                <span-->
<!--                    class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"-->
<!--                    :style="'background-image: url(\'' + photoPreview + '\');'"/>-->
<!--            </div>-->

<!--            <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">-->
<!--                Select A New Photo-->
<!--            </SecondaryButton>-->

<!--            <SecondaryButton-->
<!--                v-if="user.profile_photo_path"-->
<!--                type="button"-->
<!--                class="mt-2"-->
<!--                @click.prevent="deletePhoto">-->
<!--                Remove Photo-->
<!--            </SecondaryButton>-->

<!--            <InputError :message="form.errors.photo" class="mt-2" />-->
<!--        </div>-->
<!--    </div>-->

    <div class="flex items-center">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="emits('save-user-info')">
            Update
        </PrimaryButton>

        <ActionMessage :on="form.recentlySuccessful">
            Updated.
        </ActionMessage>
    </div>
</template>

<script setup>
//import { router } from '@inertiajs/vue3'
import { defineEmits, inject, ref } from 'vue'
import DatepickerInput from '@/Components/Form/DatepickerInput.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'

import ActionMessage from '@/Components/ActionMessage.vue'
import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'


const form = inject('form')
const emits = defineEmits(['save-user-info'])

//const { route } = window

//const photoPreview = ref(null)
const photoInput = ref(null)

// const clearPhotoFileInput = () => {
//     if (photoInput.value?.value) {
//         photoInput.value.value = null
//     }
// }

// const selectNewPhoto = () => {
//     photoInput.value.click()
// }

// const updatePhotoPreview = () => {
//     const photo = photoInput.value.files[0]
//
//     if (!photo) return
//
//     const reader = new FileReader()
//
//     reader.onload = (e) => {
//         photoPreview.value = e.target.result
//     }
//
//     reader.readAsDataURL(photo)
// }

// const deletePhoto = () => {
//     router.delete(route('current-user-photo.destroy'), {
//         preserveScroll: true,
//         onSuccess: () => {
//             photoPreview.value = null
//             clearPhotoFileInput()
//         },
//     })
// }
</script>
