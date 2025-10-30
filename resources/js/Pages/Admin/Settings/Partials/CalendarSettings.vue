<template>
    <form @submit.prevent="submit">
        <FormSection
            title="Calendar Settings"
            description="Configure default calendar preferences and display options."
        >
            <div class="space-y-4">
                <!-- Default Trainer -->
                <div>
                    <SelectInput
                        v-model="form.calendar.default_trainer_id"
                        :options="trainerOptions"
                        placeholder="Default trainer filter"
                    />
                    <InputError :message="form.errors['calendar.default_trainer_id']" />
                </div>

                <!-- Week Days -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <SelectInput
                            v-model="form.calendar.start_day"
                            :options="dayOptions"
                            placeholder="Week start"
                        />
                        <InputError :message="form.errors['calendar.start_day']" />
                    </div>
                    <div>
                        <SelectInput
                            v-model="form.calendar.end_day"
                            :options="dayOptions"
                            placeholder="Week end"
                        />
                        <InputError :message="form.errors['calendar.end_day']" />
                    </div>
                </div>

                <!-- Start Time -->
                <div>
                    <div class="flex space-x-2">
                        <SelectInput
                            v-model.number="form.calendar.start_hour"
                            :options="hourOptions"
                            size="auto"
                            placeholder="Hour"
                        />
                        <SelectInput
                            v-model="form.calendar.start_period"
                            :options="periodOptions"
                            size="auto"
                        />
                    </div>
                    <InputError :message="form.errors['calendar.start_hour'] || form.errors['calendar.start_period']" />
                </div>

                <!-- End Time -->
                <div>
                    <div class="flex space-x-2">
                        <SelectInput
                            v-model.number="form.calendar.end_hour"
                            :options="hourOptions"
                            size="auto"
                            placeholder="Hour"
                        />
                        <SelectInput
                            v-model="form.calendar.end_period"
                            :options="periodOptions"
                            size="auto"
                        />
                    </div>
                    <InputError :message="form.errors['calendar.end_hour'] || form.errors['calendar.end_period']" />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-2">
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                    >
                        Save Settings
                    </PrimaryButton>
                </div>
            </div>
        </FormSection>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

import FormSection from '@/Components/Form/FormSection.vue'
import InputError from '@/Components/Form/InputError.vue'
import PrimaryButton from '@/Components/Layout/PrimaryButton.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'

const { route } = window

const props = defineProps({
    trainers: { type: Array, required: true },
    settings: { type: Object, required: true },
})

// Form setup
const form = useForm({
    calendar: {
        default_trainer_id: props.settings.calendar?.default_trainer_id ?? null,
        start_day: props.settings.calendar?.start_day ?? 'monday',
        end_day: props.settings.calendar?.end_day ?? 'saturday',
        start_hour: props.settings.calendar?.start_hour ?? 6,
        start_period: props.settings.calendar?.start_period ?? 'AM',
        end_hour: props.settings.calendar?.end_hour ?? 10,
        end_period: props.settings.calendar?.end_period ?? 'PM',
    },
})

// Options - transform trainers array to include names
const trainerOptions = [
    { value: null, name: 'None (Show all trainers)' },
    ...props.trainers.map(trainer => ({
        value: trainer.value,
        name: trainer.label,
    })),
]

const dayOptions = [
    { value: 'monday', name: 'Monday' },
    { value: 'tuesday', name: 'Tuesday' },
    { value: 'wednesday', name: 'Wednesday' },
    { value: 'thursday', name: 'Thursday' },
    { value: 'friday', name: 'Friday' },
    { value: 'saturday', name: 'Saturday' },
    { value: 'sunday', name: 'Sunday' },
]

const hourOptions = [
    { value: 1, name: '1' },
    { value: 2, name: '2' },
    { value: 3, name: '3' },
    { value: 4, name: '4' },
    { value: 5, name: '5' },
    { value: 6, name: '6' },
    { value: 7, name: '7' },
    { value: 8, name: '8' },
    { value: 9, name: '9' },
    { value: 10, name: '10' },
    { value: 11, name: '11' },
    { value: 12, name: '12' },
]

const periodOptions = [
    { value: 'AM', name: 'AM' },
    { value: 'PM', name: 'PM' },
]

const submit = () => {
    form.patch(route('admin.settings.update'), {
        preserveScroll: true,
    })
}
</script>
