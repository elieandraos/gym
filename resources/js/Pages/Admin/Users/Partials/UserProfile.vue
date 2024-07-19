<template>
    <div>
        <h3 class="font-bold text-sm pb-1 mb-1">Summary</h3>
        <table class="text-left text-sm/6">
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Gender</td>
                <td class="py-2">
                    {{ gender }}
                </td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Birthdate</td>
                <td class="py-2">{{ birthdate }} ({{ age }} years old)</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Weight</td>
                <td class="py-2">{{ weight }} kg</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Height</td>
                <td class="py-2">{{ height }} cm</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Blood type</td>
                <td class="py-2">{{ blood_type }}</td>
            </tr>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
})

const {
    name, gender, age, birthdate, weight, height, blood_type, in_house,
} = props.user
</script>
