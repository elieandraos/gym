<template>
    <AppLayout title="Body Composition">
        <Container>
            <PageHeader :sticky="true">
                <MemberHeader :member="member"></MemberHeader>
            </PageHeader>

            <FormSection title="Date" description="Select the date when this photo was taken.">
                <DateInput v-model="form.taken_at"></DateInput>
                <InputError :message="form.errors.taken_at" />
            </FormSection>

            <FormSection title="Photo" description="Upload a body composition photo.">
                <input
                    ref="photoInput"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg"
                    @change="handlePhotoChange"
                    class="w-full p-2 border border-zinc-200 placeholder-zinc-400 hover:border-zinc-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm"
                />
                <InputError :message="form.errors.photo" />
            </FormSection>

            <div class="text-right space-x-4">
                <Link :href="route('admin.members.show', member.id)">
                    <TransparentButton>Cancel</TransparentButton>
                </Link>
                <PrimaryButton @click="uploadBodyComposition" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Upload</PrimaryButton>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AppLayout from '@/Layouts/AppLayout.vue'
import Container from '@/Components/Layout/Container.vue'
import DateInput from '@/Components/Form/DateInput.vue'
import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import MemberHeader from '@/Pages/Admin/Members/Partials/MemberHeader.vue'
import PageHeader from '@/Components/Layout/PageHeader.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import TransparentButton from '@/Components/Layout/TransparentButton.vue'

const props = defineProps({
    member: { type: Object, required: true },
    defaultDate: { type: String, required: true },
})

const { route } = window
const photoInput = ref(null)

const form = useForm({
    taken_at: props.defaultDate,
    photo: null,
})

const handlePhotoChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.photo = file
    }
}

const uploadBodyComposition = () => {
    form.post(route('admin.members.body-composition.store', props.member.id), {
        preserveScroll: true,
        onError: (e) => {
            console.log(e)
        },
    })
}
</script>
