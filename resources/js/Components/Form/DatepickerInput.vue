<template>
    <datepicker
        :modelValue="modelValue"
        @update:modelValue="updateValue"
        :monday-first="true"
        icon-color="#d4d4d8"
        icon-width="19"
        icon-height="19"
    />
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import Datepicker from 'vuejs3-datepicker';

const props = defineProps({
    modelValue: [String, Date]
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};
</script>
