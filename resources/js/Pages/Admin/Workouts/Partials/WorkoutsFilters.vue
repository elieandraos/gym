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
                <label class="text-xs text-[#71717b] mb-1 block">Categories</label>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <label
                        v-for="cat in categories"
                        :key="cat"
                        class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 p-1 rounded"
                    >
                        <input
                            type="checkbox"
                            :value="cat"
                            :checked="selectedCategories.includes(cat)"
                            @change="toggleCategory(cat)"
                            class="h-4 w-4 rounded border-zinc-200 accent-black focus:ring-black cursor-pointer"
                        />
                        <span class="text-sm">{{ cat }}</span>
                    </label>
                </div>
            </div>
        </div>
    </dropdown>
</template>

<script setup>
import { FunnelIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Layout/Dropdown.vue'

const props = defineProps({
    selectedCategories: { type: Array, required: true },
    categories: { type: Array, required: true },
})

const emit = defineEmits(['update:categories'])

const toggleCategory = (category) => {
    const current = [...props.selectedCategories]
    const index = current.indexOf(category)

    if (index > -1) {
        current.splice(index, 1)
    } else {
        current.push(category)
    }

    emit('update:categories', current)
}
</script>
