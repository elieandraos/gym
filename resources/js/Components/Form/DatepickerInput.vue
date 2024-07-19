<template>
    <div class="p-1.5 border border-zinc-200 hover:border-zinc-300 rounded-lg text-sm flex items-center">
        <datepicker
            v-model="model"
            :monday-first="true"
            icon-color="#d4d4d8"
            icon-width="18"
            icon-height="18"
            v-bind="$attrs"
            class="mt-0.5"
        />
    </div>
</template>

<script setup>
import Datepicker from 'vuejs3-datepicker'

const model = defineModel()
</script>
