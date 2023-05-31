<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="tw-block tw-font-medium tw-text-sm tw-text-gray-700">
        <span class="tw-flex tw-items-center tw-justify-start">
            {{ value }} <span v-if="required" class="tw-text-red-500 -tw-mt-3 tw-text-lg">*</span>
        </span>
    </label>
</template>
