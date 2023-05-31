<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link
        class="tw-block tw-w-full tw-px-4 tw-py-2 tw-text-left tw-text-sm tw-leading-5 tw-text-gray-700 tw-bg-white hover:tw-bg-gray-300 focus:tw-outline-none tw-transition tw-duration-150 tw-ease-in-out"
    >
        <slot />
    </Link>
</template>
