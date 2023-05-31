<template>
    <GuestLayout>
        <Head title="Criar conta" />

        <form @submit.prevent="submit">
            <q-input
                required
                outlined
                label="Primeiro Nome"
                v-model="form.name"
                :error-message="form.errors.name"
                :error="!!form.errors.name"
            />

            <q-input
                required
                outlined
                label="Email"
                type="email"
                v-model="form.email"
                :error-message="form.errors.email"
                :error="!!form.errors.email"
            />

            <q-input
                required
                outlined
                label="Senha"
                type="password"
                v-model="form.password"
                :error-message="form.errors.password"
                :error="!!form.errors.password"
            />

            <q-input
                required
                outlined
                label="Confirmar Senha"
                type="password"
                v-model="form.password_confirmation"
                :error-message="form.errors.password_confirmation"
                :error="!!form.errors.password_confirmation"
            />

            <div class="tw-flex tw-items-center tw-justify-end tw-gap-5">
                <Link
                    :href="route('login')"
                    class="tw-underline tw-text-sm tw-text-gray-600 hover:tw-text-gray-900 tw-rounded-md focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500"
                >
                    Já tem uma conta?
                </Link>

                <PrimaryButton
                    type="submit"
                    class="tw-px-4 tw-py-3"
                    background="positive"
                    text="Criar Conta"
                    :disabled="form.processing"
                />
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
