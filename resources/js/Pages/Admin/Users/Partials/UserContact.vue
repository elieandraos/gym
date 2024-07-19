<template>
    <div>
        <h3 class="font-bold text-sm pb-2 mb-2">Contact</h3>
        <table class="text-left text-sm">
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Email</td>
                <td class="py-2">{{ email }}</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Phone number</td>
                <td class="py-2">{{ phone_number }}</td>
            </tr>
            <tr class="border-b border-zinc-100">
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Location</td>
                <td class="py-2">{{ address }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 py-2">Instagram handle</td>
                <td class="py-2">
                    <a :href="instagram_url" class="text-pink-500 hover:text-blue-700" target="_blank" v-if="instagram_handle ">@{{ instagram_handle }}</a>
                </td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Emergency contact</td>
                <td class="pt-2.5">{{ emergency_contact }}</td>
            </tr>
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
