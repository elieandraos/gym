<template>
    <div class="flex justify-between items-center md:pt-5 md:mb-8">
        <h1 class="text-zinc-900 font-medium text-lg lg:text-xl capitalize">
            <slot></slot>
        </h1>
        <div>
            <slot name="buttons"></slot>
        </div>
    </div>
</template>
