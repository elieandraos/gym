<template>
    <div>users list</div>

    {{ users }}
</template>

<script setup>
defineProps({
    users: Array
})
</script>
