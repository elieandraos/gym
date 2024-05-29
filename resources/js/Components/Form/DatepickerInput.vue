<template>
    <datepicker
        v-model="model"
        :monday-first="true"
        icon-color="#d4d4d8"
        icon-width="19"
        icon-height="19"
    />
</template>

<script setup>
import Datepicker from 'vuejs3-datepicker'

const model = defineModel()
</script>
