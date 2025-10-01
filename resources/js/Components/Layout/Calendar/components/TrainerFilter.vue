<template>
    <div class="flex items-center space-x-4">
        <template v-for="{id, first_name, color} in availableTrainers" :key="id">
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input
                    type="checkbox"
                    :value="id"
                    :checked="modelValue.includes(Number(id))"
                    @change="updateSelection(id, $event)"
                    class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                />
                <span class="ml-2 cursor-pointer" :class="getTextColorClass(color)">{{ first_name }}</span>
            </label>
        </template>
    </div>
</template>

<script setup>
import { useColorScheme } from '../composables/useColorScheme.js'

const { getColorScheme } = useColorScheme()

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

const getTextColorClass = (bgColor) => {
    return getColorScheme(bgColor).text
}

const updateSelection = (trainerId, event) => {
    const newSelection = [...props.modelValue]

    // Ensure trainerId is a number to match backend format
    const trainerIdNumber = Number(trainerId)

    if (event.target.checked) {
        if (!newSelection.includes(trainerIdNumber)) {
            newSelection.push(trainerIdNumber)
        }
    } else {
        const index = newSelection.indexOf(trainerIdNumber)
        if (index > -1) {
            newSelection.splice(index, 1)
        }
    }

    emit('update:modelValue', newSelection)
    emit('filterChange')
}
</script>
