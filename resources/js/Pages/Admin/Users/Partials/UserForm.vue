<template>
    <div class="space-y-12">
        <div>
            <page-section-title>Profile info</page-section-title>
            <div class="grid grid-cols-2 hap-4 lg:grid-cols-4 lg:gap-8 my-4">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text" autofocus/>
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="gender" value="Gender" />
                    <SelectInput v-model="form.gender" id="gender" :options="genders" />
                    <InputError :message="form.errors.gender" />
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
                    <SelectInput v-model="form.blood_type" id="blood_type" :options="bloodTypes" />
                    <InputError :message="form.errors.blood_type" />
                </div>
            </div>
        </div>

        <div>
            <page-section-title>Contact info</page-section-title>
            <div class="grid grid-cols-2 hap-4 lg:grid-cols-4 lg:gap-8 my-4">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="text"/>
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel for="phone_number" value="Phone number" />
                    <TextInput id="email" v-model="form.phone_number" type="text"/>
                    <InputError :message="form.errors.phone_number" />
                </div>
                <div>
                    <InputLabel for="instagram_handle" value="Instagram handle" />
                    <TextInput id="email" v-model="form.instagram_handle" type="text"/>
                    <InputError :message="form.errors.instagram_handle" />
                </div>
                <div>
                    <InputLabel for="address" value="Address" />
                    <TextInput id="address" v-model="form.address" type="text"/>
                    <InputError :message="form.errors.address" />
                </div>
                <div>
                    <InputLabel for="emergency_contact" value="Emergency Contact" />
                    <TextInput id="emergency_contact" v-model="form.emergency_contact" type="text"/>
                    <InputError :message="form.errors.emergency_contact" />
                </div>
            </div>
        </div>

        <div>
            <page-section-title>Membership info</page-section-title>
            <div class="grid grid-cols-2 hap-4 lg:grid-cols-4 lg:gap-8 my-4">
                <div>
                    <InputLabel for="email" value="In-house member" />
                    <TextInput id="email" v-model="form.email" type="text"/>
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel for="phone_number" value="Registration date" />
                    <TextInput id="email" v-model="form.phone_number" type="text"/>
                    <InputError :message="form.errors.phone_number" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import PageSectionTitle from '@/Components/Layout/PageSectionTitle.vue'
import { inject } from 'vue'

import InputError from '@/Components/Form/InputError.vue'
import InputLabel from '@/Components/Form/InputLabel.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextInput from '@/Components/Form/TextInput.vue'

const form = inject('form')

defineProps({
    bloodTypes: { type: Array, required: true },
    genders: { type: Array, required: true },
})
</script>
