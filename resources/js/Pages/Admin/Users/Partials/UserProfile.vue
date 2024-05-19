<template>
    <div>
        <h3 class="font-medium pb-2 border-b border-b-zinc-200">Profile summary</h3>
        <table class="text-left text-sm/6">
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">About</td>
                <td class="font-medium">{{ name }}, {{ gender }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Birthdate</td>
                <td class="font-medium">{{ birthdate }} ({{ age }} years old)</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Weight</td>
                <td class="font-medium">{{ weight }}kg</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Height</td>
                <td class="font-medium">{{ height }}cm</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Blood type</td>
                <td class="font-medium">{{ blood_type }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Start date</td>
                <td class="font-medium">{{ since }}</td>
            </tr>

        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
})

const {
    name, gender, age, since, birthdate, weight, height, blood_type,
} = props.user
</script>
