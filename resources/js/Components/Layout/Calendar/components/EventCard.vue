<template>
    <component
        :is="slot.members.length === 1 ? 'a' : 'div'"
        v-bind="slot.members.length === 1
            ? { href: slot.url }
            : {}"
        @click="slot.members.length > 1 ? $emit('openModal', slot) : null"
        class="group absolute flex flex-col overflow-y-auto rounded-lg p-2 text-xs hover:opacity-90 cursor-pointer"
        :class="slot.bgClass"
        :style="{
            left: slot.overlapCount > 1
              ? 'calc(' + slot.overlapIndex + '*(100%/' + slot.overlapCount + ') + 0.25rem)'
              : '0.25rem',
            width: slot.overlapCount > 1
              ? 'calc((100%/' + slot.overlapCount + ') - 0.5rem)'
              : 'calc(100% - 0.5rem)',
            top: 'calc(' + slot.topPercent + '% + 1px)',
            height: 'calc(' + slot.heightPercent + '% - 2px)',
            zIndex: slot.overlapCount - slot.overlapIndex
          }"
    >
        <p
            class="text-xs"
            :class="slot.textClass + (slot.members.length === 1 ? ' group-hover:' + slot.hoverText : '')"
        >
            <time :datetime="slot.start_time">{{ slot.short_time }}</time>
        </p>
        <p class="font-semibold mt-2" :class="slot.textClass">
            {{ slot.members.join(', ') }}
        </p>
    </component>
</template>

<script setup>
defineProps({
    slot: {
        type: Object,
        required: true
    }
})

defineEmits(['openModal'])
</script>