<template>
    <div class="tw-min-h-screen tw-flex tw-flex-col sm:tw-justify-center tw-items-center tw-pt-6 sm:tw-pt-0 tw-bg-gray-100">
        <Link :href="route('index')">
            <ApplicationLogo class="tw-w-full tw-h-20" />
        </Link>

        <div class="tw-w-full sm:tw-max-w-md tw-mt-6 tw-px-6 tw-py-4 tw-bg-white tw-shadow-md tw-overflow-hidden sm:tw-rounded-lg">
            <slot />
        </div>
    </div>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<style>
[type='text']:focus, [type='email']:focus, [type='url']:focus, [type='password']:focus,
[type='number']:focus, [type='date']:focus, [type='datetime-local']:focus, [type='month']:focus,
[type='search']:focus, [type='tel']:focus, [type='time']:focus, [type='week']:focus, [multiple]:focus,
textarea:focus, select:focus{
    --tw-ring-shadow: 0;
}
</style>
