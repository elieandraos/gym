<template>
    <div>users list</div>

    <pre>{{ users }}</pre>
</template>

<script setup>
defineProps({
    users: Array
})
</script>
