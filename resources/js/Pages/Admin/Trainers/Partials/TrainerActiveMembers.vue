<template>
    <div>
        <p>
            Training
            <span
                v-for="(name, index) in displayNames"
                :key="index"
            >
        {{ name }}<span v-if="shouldShowComma(index)">, </span>
      </span>
            <span
                v-if="hasAdditional && !expanded"
                @click="toggleExpanded"
                class="text-blue-500 cursor-pointer"
            >
        {{ additionalText }}
      </span>
            .
        </p>
    </div>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue';

// Define incoming props
const props = defineProps({
    activeMemberNames: {
        type: Array,
        required: true,
    },
    activeMembersAdditionalCount: {
        type: Number,
        required: true,
    },
    activeMemberFullNames: {
        type: Array,
        required: true,
    },
});

// Local state
const expanded = ref(false);

// Computed for names to display
const displayNames = computed(() =>
    expanded.value ? props.activeMemberFullNames : props.activeMemberNames
);

// Whether there are additional members
const hasAdditional = computed(() => props.activeMembersAdditionalCount > 0);

// Text for the "+ X other members"
const additionalText = computed(() =>
    ` and ${props.activeMembersAdditionalCount} other member${
        props.activeMembersAdditionalCount > 1 ? 's' : ''
    }`
);

// Toggle expansion
function toggleExpanded() {
    expanded.value = !expanded.value;
}

// Comma logic between names
function shouldShowComma(index) {
    return index < displayNames.value.length - 1;
}
</script>
