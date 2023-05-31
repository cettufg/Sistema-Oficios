<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    usuario: Object
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <div class="tw-py-12">
            <div class="tw-max-w-7xl tw-mx-auto sm:tw-px-6 lg:tw-px-8 tw-space-y-6">
                <q-banner inline-actions class="text-white bg-red text-center" v-if="usuario.password_reset">
                    Você precisa atualizar a sua senha!
                </q-banner>
                <div class="tw-p-4 sm:tw-p-8 tw-bg-white tw-shadow sm:tw-rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="tw-max-w-xl"
                    />
                </div>

                <div class="tw-p-4 sm:tw-p-8 tw-bg-white tw-shadow sm:tw-rounded-lg">
                    <UpdatePasswordForm class="tw-max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
