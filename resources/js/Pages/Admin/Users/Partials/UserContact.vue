<template>
    <div>
        <h3 class="font-medium pb-2 mb-2 border-b border-b-zinc-200">Contact information</h3>
        <table class="text-left text-sm/6">
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Email</td>
                <td class="font-medium pt-2.5">{{ email }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Phone number</td>
                <td class="font-medium pt-2.5">{{ phone_number }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Address</td>
                <td class="font-medium pt-2.5">{{ address }}</td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Instagram handle</td>
                <td class="font-medium pt-2.5">
                    <a :href="instagram_url" class="text-pink-500 hover:text-blue-700" target="_blank" v-if="instagram_handle ">@{{ instagram_handle }}</a>
                </td>
            </tr>
            <tr>
                <td class="text-zinc-400 w-32 lg:w-48 pt-2.5">Emergency contact</td>
                <td class="font-medium pt-2.5">{{ emergency_contact }}</td>
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
