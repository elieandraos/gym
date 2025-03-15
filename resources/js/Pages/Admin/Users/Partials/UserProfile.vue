<template>
    <div class="w-full">
        <h3 class="font-semibold text-sm pb-1 mb-1">Summary</h3>
        <table class="text-left text-sm w-full">
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-4">Gender</td>
                <td class="py-4">
                    {{ gender }}
                </td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-4">Birthdate</td>
                <td class="py-4">{{ birthdate }}</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-4">Weight</td>
                <td class="py-4">{{ weight }} kg</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-4">Height</td>
                <td class="py-4">{{ height }} cm</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 py-4">Blood type</td>
                <td class="py-4">{{ blood_type }}</td>
            </tr>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
})

const {
    gender, birthdate, weight, height, blood_type,
} = props.user
</script>
