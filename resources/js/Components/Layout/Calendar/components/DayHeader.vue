<template>
    <div
        class="sticky top-[12px] z-40 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8"
    >
        <div
            class="-mr-px hidden grid-cols-6 divide-x divide-gray-100 border-r border-gray-100 text-sm text-gray-500 sm:grid"
        >
            <div class="col-end-1 w-14"></div>
            <template v-for="day in headerDays" :key="day.day">
                <div class="flex items-center justify-center py-3">
                    <span class="text-gray-900">
                        {{ day.short }}
                        <span
                            class="ml-1 inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="day.isToday ? 'bg-black text-white' : 'text-gray-700'"
                        >
                            {{ day.day }}
                        </span>
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
defineProps({
    headerDays: {
        type: Array,
        default: () => []
    }
})
</script>
