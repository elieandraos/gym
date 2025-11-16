<template>
    <dropdown direction="left">
        <template #trigger>
            <button class="p-2 hover:bg-zinc-100 rounded-lg cursor-pointer" type="button">
                <FunnelIcon class="w-5 h-5 text-zinc-500 pointer-events-none" />
            </button>
        </template>
        <div class="space-y-3">
            <div class="text-xs font-semibold text-zinc-700 uppercase">Filters</div>
            <div class="space-y-2">
                <label class="text-xs text-[#71717b] mb-1 block">Training Status</label>
                <div class="space-y-2">
                    <label
                        class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 p-1 rounded"
                    >
                        <input
                            type="radio"
                            value="all"
                            :checked="trainingStatus === 'all'"
                            @change="handleChange('all')"
                            class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm">All</span>
                    </label>
                    <label
                        class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 p-1 rounded"
                    >
                        <input
                            type="radio"
                            value="active"
                            :checked="trainingStatus === 'active'"
                            @change="handleChange('active')"
                            class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm">Active</span>
                    </label>
                    <label
                        class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 p-1 rounded"
                    >
                        <input
                            type="radio"
                            value="dormant"
                            :checked="trainingStatus === 'dormant'"
                            @change="handleChange('dormant')"
                            class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm">Dormant</span>
                    </label>
                </div>
            </div>
        </div>
    </dropdown>
</template>

<script setup>
import { FunnelIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Layout/Dropdown.vue'

defineProps({
    trainingStatus: { type: String, required: true },
})

const emit = defineEmits(['update:trainingStatus'])

const handleChange = (value) => {
    emit('update:trainingStatus', value)
}
</script>
