<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Notify } from 'quasar';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            Notify.create({
                message: 'Senha atualizada com sucesso!',
                type: 'positive',
            })
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
            Notify.create({
                message: 'Erro ao atualizar sua senha!',
                type: 'negative',
            })
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="tw-text-lg tw-font-medium tw-text-gray-900">Atualizar senha</h2>

            <p class="tw-mt-1 tw-text-sm tw-text-gray-600">
                Certifique-se de que sua conta esteja usando uma senha longa e aleatória para se manter segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="tw-mt-6 tw-space-y-6">
            <div>
                <InputLabel for="current_password" value="Senha atual" />

                <q-input
                    outlined
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="current-password"
                    :error-message="form.errors.current_password"
                    :error="!!form.errors.current_password"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nova senha" />

                <q-input
                    outlined
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="new-password"
                    :error-message="form.errors.password"
                    :error="!!form.errors.password"
                />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirmar senha" />

                <q-input
                    outlined
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="new-password"
                    :error-message="form.errors.password_confirmation"
                    :error="!!form.errors.password_confirmation"
                />
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
