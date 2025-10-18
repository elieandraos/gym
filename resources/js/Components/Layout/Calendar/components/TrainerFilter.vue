<template>
    <div class="flex items-center space-x-4 mr-8 lg:mr-0">
        <template v-for="{id, first_name, color} in availableTrainers" :key="id">
            <label class="inline-flex items-center text-sm cursor-pointer">
                <input
                    type="checkbox"
                    :value="id"
                    :checked="modelValue.includes(Number(id))"
                    @change="updateSelection(id, $event)"
                    class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                />
                <span class="ml-1 font-[500] cursor-pointer" :class="getTextColor(color)">{{ first_name }}</span>
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

const getTextColor = (bgColor) => {
    const colorMap = {
        'bg-blue-50': 'text-blue-300',
        'bg-amber-100': 'text-amber-300',
        'bg-pink-50': 'text-pink-300',
        'bg-green-50': 'text-green-300',
        'bg-gray-100': 'text-gray-300',
        'bg-purple-100': 'text-purple-300',
        'bg-cyan-100': 'text-cyan-300',
        'bg-yellow-100': 'text-yellow-300',
        'bg-lime-100': 'text-lime-300',
        'bg-red-100': 'text-red-300',
    }
    return colorMap[bgColor] || 'text-gray-100'
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
