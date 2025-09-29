<template>
    <div class="flex items-center space-x-4">
        <template v-for="{id, first_name} in availableTrainers" :key="id">
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input
                    type="checkbox"
                    :value="id"
                    :checked="modelValue.includes(id)"
                    @change="updateSelection(id, $event)"
                    class="h-4 w-4 rounded border-gray-300 accent-black focus:ring-black cursor-pointer"
                />
                <span class="ml-2 text-gray-700 cursor-pointer">{{ first_name }}</span>
            </label>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    availableTrainers: {
        type: Array,
        default: () => []
    },
    modelValue: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update:modelValue', 'filterChange'])

const updateSelection = (trainerId, event) => {
    const newSelection = [...props.modelValue]

    if (event.target.checked) {
        if (!newSelection.includes(trainerId)) {
            newSelection.push(trainerId)
        }
    } else {
        const index = newSelection.indexOf(trainerId)
        if (index > -1) {
            newSelection.splice(index, 1)
        }
    }

    emit('update:modelValue', newSelection)
    emit('filterChange')
}
</script>
