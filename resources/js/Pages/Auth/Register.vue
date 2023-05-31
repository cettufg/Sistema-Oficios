<template>
    <GuestLayout>
        <Head title="Criar conta" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Primeiro Nome" />

                <input
                    id="name"
                    type="text"
                    class="tw-mt-1 tw-block tw-w-full tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="tw-mt-2" :message="form.errors.name" />
            </div>

            <div class="tw-mt-4">
                <InputLabel for="email" value="E-mail" />

                <input
                    id="email"
                    type="email"
                    class="tw-mt-1 tw-block tw-w-full tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="tw-mt-2" :message="form.errors.email" />
            </div>

            <div class="tw-mt-4">
                <InputLabel for="password" value="Senha" />

                <input
                    id="password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="tw-mt-2" :message="form.errors.password" />
            </div>

            <div class="tw-mt-4">
                <InputLabel for="password_confirmation" value="Confirmar senha" />

                <input
                    id="password_confirmation"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="tw-mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="tw-flex tw-items-center tw-justify-end tw-mt-4">
                <Link
                    :href="route('login')"
                    class="tw-underline tw-text-sm tw-text-gray-600 hover:tw-text-gray-900 tw-rounded-md focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500"
                >
                    Já tem uma conta?
                </Link>

                <Button class="tw-ml-4" :class="{ 'opacity-25': form.processing, 'tw-bg-green-500 hover:tw-bg-green-700 focus:tw-bg-green-500 active:tw-bg-green-700': !form.processing }" :disabled="form.processing">
                    Criar conta
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
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
