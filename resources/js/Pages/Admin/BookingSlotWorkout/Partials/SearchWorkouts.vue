<template>
    <div>
        <TextInput v-model="searchValue" placeholder="Search workouts..." class="w-full" />
    </div>
</template>

<script setup>
import TextInput from '@/Components/Form/TextInput.vue'
import { inject, computed } from 'vue'

const { search } = inject('workoutState')

const searchValue = computed({
    get: () => search.value,
    set: (value) => { search.value = value }
})
</script>