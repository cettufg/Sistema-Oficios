<template>
    <Head title="Adicionar Destinatário" />

    <AuthenticatedLayout>
        <div class="tw-py-12">
            <div class="md:tw-mx-20">
                <div class="tw-overflow-hidden tw-px-3">
                    <div class="tw-flex tw-items-center tw-mb-10">
                        <span class="tw-text-3xl tw-mr-7">Destinatário</span>
                        <Link class="tw-flex tw-items-center tw-justify-center tw-mt-1" :href="route('destinatario.index')">
                            <Icon icon="ic:baseline-keyboard-arrow-left" /> <span class="ml-1">Voltar</span>
                        </Link>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <label for="">Destinatário</label>
                            <q-input v-model="form.nome"
                                counter
                                outlined
                                maxlength="255"
                                :error-message="form.errors.nome" :error="!!form.errors.nome"
                            />
                        </div>

                        <div>
                            <label for="">Status</label>
                            <q-select v-model="form.status"
                                outlined
                                :options="['Ativo', 'Inativo']"
                                :error-message="form.errors.status" :error="!!form.errors.status"
                            />
                        </div>

                    </div>

                    <div class="tw-flex tw-items-center">
                        <q-btn color="primary"
                            :disable="enviando"
                            text-color="white"
                            class="tw-mt-5 tw-w-40"
                            @click="save()"
                        >
                            <q-spinner
                                v-if="enviando"
                                color="white"
                                size="1em"
                            />
                            <span v-if="!enviando" class="tw-flex tw-items-center">Cadastrar <Icon class="tw-ml-1 tw-text-xl" icon="material-symbols:add-circle-outline" /></span>
                        </q-btn>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

const enviando = ref(false);
const form = useForm({
    nome: '',
    status: 'Ativo'
})

function save(){
    enviando.value = true;
    form.post(route('destinatario.store'),{
        preserveScroll: true,
        onSuccess: (response) => {
            enviando.value = false;
        },
        onError: () => {
            enviando.value = false;
        },
        onFinish: () => '',
    });
}
</script>
