<template>
    <div class="px-1 py-0.5 w-10 bg-zinc-100 border border-zinc-200 rounded-full cursor-pointer" @click="toggle">
        <div
            :class="{'translate-x-5 bg-zinc-800': modelValue, 'bg-white': !modelValue}"
            class="transform transition-transform w-4 h-4 rounded-full">
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
    modelValue: Boolean
});

const emit = defineEmits(['update:modelValue']);

const localValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    localValue.value = newValue;
});

const toggle = () => {
    localValue.value = !localValue.value;
    emit('update:modelValue', localValue.value);
};
</script>

<style scoped>
.transform {
    transition: transform 0.2s ease-in-out;
}
.translate-x-5 {
    transform: translateX(1rem); /* Move the switch to the right */
}
</style>
