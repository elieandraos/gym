<template>
    <div class="bg-zinc-50 rounded-lg p-3 flex justify-between items-center">
        <!-- Editable Circuit Name -->
        <input
            v-if="isEditing"
            ref="inputRef"
            v-model="editedName"
            @blur="saveName"
            @keyup.enter="saveName"
            @keyup.esc="cancelEdit"
            class="flex-1 bg-transparent border-none focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-2 py-1 font-medium text-zinc-800"
            autofocus
        />
        <div
            v-else
            @click="startEdit"
            class="flex-1 px-2 py-1 font-medium text-zinc-800 cursor-pointer hover:bg-zinc-100 rounded transition-colors"
        >
            {{ circuit.name }}
        </div>

        <!-- Delete Button -->
        <button
            @click="deleteCircuit"
            class="ml-2 p-1.5 text-zinc-500 hover:text-red-500 hover:bg-zinc-100 rounded transition-colors cursor-pointer"
            title="Delete circuit"
        >
            <TrashIcon class="w-4 h-4 cursor-pointer" />
        </button>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    circuit: { type: Object, required: true },
    bookingSlotId: { type: Number, required: true },
})

const emit = defineEmits(['updated', 'deleted'])

const isEditing = ref(false)
const editedName = ref(props.circuit.name)
const inputRef = ref(null)

const startEdit = () => {
    isEditing.value = true
    editedName.value = props.circuit.name
    nextTick(() => {
        inputRef.value?.focus()
        inputRef.value?.select()
    })
}

const saveName = () => {
    if (editedName.value.trim() === '') {
        editedName.value = props.circuit.name
        isEditing.value = false
        return
    }

    if (editedName.value !== props.circuit.name) {
        // Emit updated circuit with new name
        emit('updated', {
            ...props.circuit,
            name: editedName.value
        })
    }

    isEditing.value = false
}

const cancelEdit = () => {
    editedName.value = props.circuit.name
    isEditing.value = false
}

const deleteCircuit = () => {
    if (confirm(`Delete "${props.circuit.name}"? All workouts will be removed.`)) {
        emit('deleted')
    }
}
</script>
