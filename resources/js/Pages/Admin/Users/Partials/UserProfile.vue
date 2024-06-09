<template>
    <div>
        <h3 class="font-medium pb-2 mb-2 border-b border-b-zinc-200 flex items-center gap-2">
            {{ name }}
            <check-badge-icon class="w-4 h-4 text-sky-500 inline" v-if="in_house"></check-badge-icon>
        </h3>
        <table class="text-left text-sm/6">
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Gender</td>
                <td class="pt-2.5">
                    {{ gender }}
                </td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Birthdate</td>
                <td class="pt-2.5">{{ birthdate }} ({{ age }} years old)</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Weight</td>
                <td class="pt-2.5">{{ weight }}kg</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Height</td>
                <td class="pt-2.5">{{ height }}cm</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Blood type</td>
                <td class="pt-2.5">{{ blood_type }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Start date</td>
                <td class="pt-2.5">{{ since }}</td>
            </tr>

        </table>
    </div>
</template>

<script setup>
import { CheckBadgeIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    user: { type: Object, required: true },
})

const {
    name, gender, age, since, birthdate, weight, height, blood_type, in_house,
} = props.user
</script>
