<template>
    <div v-show="message">
        <p class="inline-block bg-red-50 text-red-500 mt-1 p-1 text-xs" data-validation-error>
            {{ message }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    message: String,
})
</script>
