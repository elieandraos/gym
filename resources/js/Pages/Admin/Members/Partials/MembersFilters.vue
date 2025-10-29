<template>
    <dropdown direction="left">
        <template #trigger>
            <button class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer" type="button">
                <FunnelIcon class="w-5 h-5 text-zinc-500 pointer-events-none" />
            </button>
        </template>
        <div class="space-y-3">
            <div class="text-xs font-semibold text-zinc-700 uppercase">Filters</div>
            <div class="flex items-center gap-2">
                <switch-input :model-value="activeTraining" @update:model-value="handleChange" />
                <label class="text-sm text-[#71717b]">Currently training</label>
            </div>
        </div>
    </dropdown>
</template>

<script setup>
import { FunnelIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Layout/Dropdown.vue'
import SwitchInput from '@/Components/Form/SwitchInput.vue'

defineProps({
    activeTraining: { type: Boolean, required: true },
})

const emit = defineEmits(['update:activeTraining'])

const handleChange = (value) => {
    emit('update:activeTraining', value)
}
</script>
