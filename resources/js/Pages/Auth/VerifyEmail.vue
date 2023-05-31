<template>
    <GuestLayout>
        <Head title="Verificação de e-mail" />

        <div class="tw-mb-4 tw-text-sm tw-text-gray-600">
            Obrigado por inscrever-se! Antes de começar, você poderia verificar seu endereço de e-mail clicando no link
            acabamos de enviar um e-mail para você? Se você não recebeu o e-mail, teremos o prazer de lhe enviar outro.
        </div>

        <div class="tw-mb-4 tw-font-medium tw-text-sm tw-text-green-600" v-if="verificationLinkSent">
            Um novo link de verificação foi enviado para o endereço de e-mail que você forneceu durante o registro.
        </div>

        <form @submit.prevent="submit">
            <div class="tw-mt-4 tw-flex items-center tw-justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reenviar email de verificação
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="tw-underline tw-text-sm tw-text-gray-600 hover:tw-text-gray-900 tw-rounded-md focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500"
                    >Logout</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>