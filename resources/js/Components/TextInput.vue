<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue', 'placeholder']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
        :value="modelValue"
        @keyup="$emit('update:modelValue', modelValue)"
        ref="input"
    />
</template>
