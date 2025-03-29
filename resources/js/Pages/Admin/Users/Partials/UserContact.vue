<template>
    <div class="w-full">
        <h3 class="font-semibold text-sm pb-2 mb-2">Contact</h3>
        <table class="text-left text-sm w-full">
            <tbody>
                <tr class="border-b border-zinc-100">
                    <td class="text-zinc-400 w-32 lg:w-48 py-4">Email</td>
                    <td class="py-4">{{ email }}</td>
                </tr>
                <tr class="border-b border-zinc-100">
                    <td class="text-zinc-400 w-32 lg:w-48 py-4">Phone number</td>
                    <td class="py-4">{{ phone_number }}</td>
                </tr>
                <tr class="border-b border-zinc-100">
                    <td class="text-zinc-400 w-32 lg:w-48 py-4">Location</td>
                    <td class="py-4">{{ address }}</td>
                </tr>
                <tr>
                    <td class="text-zinc-400 w-32 lg:w-48 py-4">Instagram handle</td>
                    <td class="py-4">
                        <a :href="instagram_url" class="text-pink-500 hover:text-blue-700" target="_blank" v-if="instagram_handle ">@{{ instagram_handle }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-zinc-400 w-32 lg:w-48 py-4">Emergency contact</td>
                    <td class="py-4">{{ emergency_contact }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
})

const {
    email, phone_number, instagram_handle, instagram_url, address, emergency_contact,
} = props.user
</script>
