<template>
    <div
        class="border border-zinc-200 rounded-lg hover:border-zinc-300 pr-2 relative cursor-pointer"
        @click="focusSelect"
        tabindex="0"
        @keydown.enter="focusSelect"
    >
        <select
            v-model="model"
            ref="select"
            v-bind="$attrs"
            class="p-2 text-sm border-none rounded-lg focus:outline-none focus:ring-0 bg-none cursor-pointer"
            :class="size !== 'auto' ? 'min-w-36 lg:min-w-52 w-full' : 'min-w-12'"
        >
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>

        <ChevronUpDownIcon class="h-5 text-zinc-400 absolute right-2 top-2 z-50" @click.stop="focusSelect"></ChevronUpDownIcon>
    </div>

</template>

<script setup>
import { ChevronUpDownIcon} from '@heroicons/vue/24/solid/index.js'
import { ref } from 'vue'

defineProps({
    options: {
        type: Array,
        default: () => [],
        required: true
    },
    size: {
        type: String,
        default: '',
        required: false
    }
})

const model = defineModel()
const select = ref(null)

const focusSelect = () => {
    select.value.focus()
}
</script>
