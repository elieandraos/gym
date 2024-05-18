<template>
    <div>
        <h3 class="font-medium pb-2 border-b border-b-zinc-200">Contact information</h3>
        <table class="text-left text-sm/6">
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Email</td>
                <td class="font-medium">{{ email }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Phone number</td>
                <td class="font-medium">{{ phone_number }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Address</td>
                <td class="font-medium">{{ address }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Instagram handle</td>
                <td class="font-medium">{{ instagram_handle }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Emergency contact</td>
                <td class="font-medium">{{ emergency_contact }}</td>
            </tr>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true }
})

const { email, phone_number, instagram_handle, address, emergency_contact } = props.user
</script>
