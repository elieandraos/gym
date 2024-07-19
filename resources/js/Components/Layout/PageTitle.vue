<template>
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl/8 font-bold text-zinc-950 sm:text-xl/8 grow capitalize">
            <slot></slot>
        </h2>
        <slot name="buttons"></slot>
    </div>
</template>
