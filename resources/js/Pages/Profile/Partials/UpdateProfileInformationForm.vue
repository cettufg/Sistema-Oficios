<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

</script>

<template>
    <section>
        <header>
            <h2 class="tw-text-lg tw-font-medium tw-text-gray-900">Informações do perfil</h2>

            <p class="tw-mt-1 tw-text-sm tw-text-gray-600">
                Atualize as informações de perfil e o endereço de e-mail da sua conta.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="tw-mt-6 tw-space-y-6">
            <div>
                <InputLabel for="name" value="Nome" />

                <q-input
                    outlined
                    id="name"
                    type="text"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    :error="!!form.errors.name"
                    :error-message="form.errors.name"
                />
            </div>

            <div>
                <InputLabel for="email" value="E-mail" />

                <q-input
                    outlined
                    id="email"
                    type="email"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    :error="!!form.errors.email"
                    :error-message="form.errors.email"
                />

            </div>

            <div v-if="props.mustVerifyEmail && user.email_verified_at === null">
                <p class="tw-text-sm tw-mt-2 tw-text-gray-800">
                    Seu endereço de e-mail não foi verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="tw-underline tw-text-sm tw-text-gray-600 hover:tw-text-gray-900 tw-rounded-md focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>

                <div
                    v-show="props.status === 'verification-link-sent'"
                    class="tw-mt-2 tw-font-medium tw-text-sm tw-text-green-600"
                >
                    Um novo link de verificação foi enviado para o seu endereço de e-mail.
                </div>
            </div>

            <div class="tw-flex tw-items-center tw-gap-4">
                <PrimaryButton
                    type="submit"
                    class="tw-px-4 tw-py-3"
                    background="positive"
                    text="Salvar"
                    :disabled="form.processing"
                    icon="ic:round-save"
                />

                <Transition enter-from-class="tw-opacity-0" leave-to-class="tw-opacity-0" class="tw-transition tw-ease-in-out">
                    <p v-if="form.recentlySuccessful" class="tw-text-sm tw-text-gray-600">Salvo.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style>
[type='text']:focus, [type='email']:focus, [type='url']:focus, [type='password']:focus,
[type='number']:focus, [type='date']:focus, [type='datetime-local']:focus, [type='month']:focus,
[type='search']:focus, [type='tel']:focus, [type='time']:focus, [type='week']:focus, [multiple]:focus,
textarea:focus, select:focus{
    --tw-ring-shadow: 0;
}
</style>
