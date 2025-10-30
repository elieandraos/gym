<template>
    <button :type="type" class="inline-flex items-center justify-center px-[6px] py-[4px] lg:px-[12px] lg:py-[6px] bg-gray-800 border border-transparent rounded-md font-[500] text-white tracking-wider cursor-pointer hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
})
</script>
