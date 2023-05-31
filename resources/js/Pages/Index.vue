<template>
    <Head title="Sistema de ofícios" />

    <AuthenticatedLayout>
        <div class="tw-py-12">
            <div class="tw-max-w-7xl tw-mx-auto sm:tw-px-6 lg:tw-px-8">
                <div class="tw-overflow-hidden">
                    <div class="tw-p-6 tw-text-gray-900 tw-flex tw-items-center tw-justify-center">
                        <img src="@/images/content.png" alt="Content" class="content">
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
.content{
    width: 80%;
}
</style>
