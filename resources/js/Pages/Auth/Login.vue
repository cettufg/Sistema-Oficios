<template>
    <GuestLayout>
        <Head title="Entrar" />
        <form @submit.prevent="submit" class="tw-flex tw-flex-col tw-gap-4">
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

            <q-checkbox
                v-model="form.remember"
                label="Lembre-me"
            />

            <div class="tw-flex tw-items-center tw-justify-end tw-gap-5">
                <PrimaryButton
                    type="submit"
                    class="tw-px-4 tw-py-3"
                    :disabled="form.processing"
                    background="primary"
                    text="Entrar"
                />
                <PrimaryLink
                    :href="route('register')"
                    outlined
                    class="tw-px-4 tw-py-3"
                    background="primary"
                    text="Criar conta"
                />
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
