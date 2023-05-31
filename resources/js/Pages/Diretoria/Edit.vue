<template>
    <Head title="Editar Diretoria" />

    <AuthenticatedLayout>
        <div class="tw-py-12">
            <div class="tw-mx-20">
                <div class="tw-overflow-hidden tw-px-3">
                    <div class="tw-flex tw-items-center tw-mb-10">
                        <span class="tw-text-3xl tw-mr-7">Diretoria</span>
                        <Link class="tw-flex tw-items-center tw-justify-center tw-mt-1" :href="route('diretoria.index')">
                            <Icon icon="ic:baseline-keyboard-arrow-left" /> <span class="tw-ml-1">Voltar</span>
                        </Link>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div>
                            <label for="">Grupo</label>
                            <q-select v-model="form.group_id"
                                outlined
                                :options="options_group_id"
                                :error-message="form.errors.group_id" :error="!!form.errors.group_id"
                            />
                        </div>

                        <div>
                            <label for="">Nome</label>
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
                            <span v-if="!enviando" class="tw-flex tw-items-center">Salvar <Icon class="tw-ml-1 tw-text-xl" icon="material-symbols:save-outline" /></span>
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

const props = defineProps({
    diretoria: {
        default: '',
        type: Object
    },
    grupos: {
        default: '',
        type: Object,
    }
});
const options_group_id = ref([]);
const form = useForm({
    group_id: '',
    nome: '',
    status: 'Ativo'
})

const enviando = ref(false);

onMounted(() => {
    props.grupos.map((item) => {
        options_group_id.value.push({label: item.nome, id: item.id});
        if(item.id == props.diretoria.group_id){
            form.group_id = {label: item.nome, id: props.diretoria.group_id};
        }
    });
    //Preenche todos os dados do formulário
    form.id = props.diretoria.id;
    form.nome = props.diretoria.nome;
    form.status = props.diretoria.status;
});

function save(){
    enviando.value = true;
    form.put(route('diretoria.update', form.id), {
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
