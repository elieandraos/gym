<template>
    <div class="flex-1 max-w-xs">
        <text-input
            :model-value="search"
            @update:model-value="handleSearch"
            placeholder="Search trainers..."
        />
    </div>
</template>

<script setup>
import TextInput from '@/Components/Form/TextInput.vue'

defineProps({
    search: { type: String, required: true },
})

const emit = defineEmits(['update:search'])

const handleSearch = (value) => {
    emit('update:search', value)
}
</script>
