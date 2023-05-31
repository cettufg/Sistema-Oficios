<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Notify } from 'quasar';
import { onMounted } from 'vue';

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

onMounted(() => {
    if(props.status) {
        Notify.create({
            type: 'negative',
            message: props.status,
            position: 'top-right',
            timeout: 3000,
        });
    }
})

const submit = () => {
    form.post(route('login'), {
        onSuccess: (response) => {
            if(response.props.status) {
                Notify.create({
                    type: 'negative',
                    message: response.props.status,
                    position: 'top-right',
                    timeout: 3000,
                });
            }
        },
        onError: (errors) => {
            console.log(errors);
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />
        <form>
            <div>
                <InputLabel for="email" value="E-mail" />

                <input
                    id="email"
                    type="email"
                    class="tw-mt-1 tw-block tw-w-full tw-border-gray-300 focus:tw-border-gray-500 focus:tw-ring-0 tw-rounded-md tw-shadow-sm"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                />

                <InputError class="tw-mt-2" :message="form.errors.password" />
            </div>

            <div class="tw-block tw-mt-4">
                <label class="tw-flex tw-items-center">
                    <q-checkbox v-model="form.remember" />
                    <span class="tw-ml-1 tw-text-sm tw-text-gray-600">Lembre-me</span>
                </label>
            </div>

            <div class="tw-flex tw-items-center tw-justify-end tw-mt-4">
                <Link
                    v-if="props.canResetPassword"
                    :href="route('password.request')"
                    class="tw-underline tw-text-sm tw-text-gray-600 hover:tw-text-gray-900 tw-rounded-md focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500"
                >
                    Esqueceu sua senha?
                </Link>

                <Button type="submit" @click="submit()" class="tw-ml-4" :class="{ 'tw-opacity-25': form.processing, 'tw-bg-green-500 hover:tw-bg-green-700 focus:tw-bg-green-500 active:tw-bg-green-700': !form.processing }" :disabled="form.processing">
                    Entrar
                </Button>
                <Button :link="true" :href="route('register')" class="tw-ml-4">
                    Criar conta
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
